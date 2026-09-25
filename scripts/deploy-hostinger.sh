#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

# Safe in-place deployment for the MyVivahAI Hostinger account.
# Override APP_DIR and DEPLOY_BRANCH when using another checkout.
APP_DIR="${MYVIVAHAI_APP_DIR:-/home/u320743426/domains/digitalji.in/public_html/myvivahai}"
DEPLOY_BRANCH="${MYVIVAHAI_DEPLOY_BRANCH:-main}"
BACKUP_DIR="${MYVIVAHAI_BACKUP_DIR:-${HOME}/.local/share/myvivahai/backups}"
HEALTH_URL="${MYVIVAHAI_HEALTH_URL:-https://myvivahai.digitalji.in/api/v1/health/chat}"

fail() { printf 'Deploy stopped: %s\n' "$*" >&2; exit 1; }

[[ -d "$APP_DIR" ]] || fail "application directory not found: $APP_DIR"
cd "$APP_DIR"
[[ -f artisan && -f .env && -d .git ]] || fail 'run this script from the MyVivahAI Git checkout'
[[ "$(git branch --show-current)" == "$DEPLOY_BRANCH" ]] || fail "checkout must be on $DEPLOY_BRANCH"
[[ -z "$(git status --porcelain)" ]] || fail 'server checkout has local changes; inspect and commit or remove them first'

for tool in php composer git mysqldump gzip curl flock; do
    command -v "$tool" >/dev/null 2>&1 || fail "required command is missing: $tool"
done

# Validate production settings without displaying or sourcing credentials.
php -r '
require "vendor/autoload.php";
$v = Dotenv\Dotenv::parse(file_get_contents(".env"));
$required = ["APP_KEY", "DB_HOST", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD"];
foreach ($required as $key) if (!isset($v[$key]) || trim((string) $v[$key]) === "") { fwrite(STDERR, "Missing required production setting: {$key}\n"); exit(1); }
if (($v["APP_ENV"] ?? "") !== "production") { fwrite(STDERR, "APP_ENV must be production\n"); exit(1); }
if (($v["APP_DEBUG"] ?? "true") !== "false") { fwrite(STDERR, "APP_DEBUG must be false\n"); exit(1); }
if (($v["APP_URL"] ?? "") !== "https://myvivahai.digitalji.in") { fwrite(STDERR, "APP_URL does not match the production host\n"); exit(1); }
if (($v["DB_CONNECTION"] ?? "") !== "mysql") { fwrite(STDERR, "DB_CONNECTION must be mysql\n"); exit(1); }
if (!ctype_digit((string) ($v["DB_PORT"] ?? ""))) { fwrite(STDERR, "DB_PORT must be numeric\n"); exit(1); }
' || fail 'production .env validation failed'

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"
exec 9>"${TMPDIR:-/tmp}/myvivahai-deploy.lock"
flock -n 9 || fail 'another deployment is already running'

git fetch --prune origin "$DEPLOY_BRANCH"
CURRENT_REVISION="$(git rev-parse HEAD)"
TARGET_REVISION="$(git rev-parse FETCH_HEAD)"
git merge-base --is-ancestor "$CURRENT_REVISION" "$TARGET_REVISION" || fail 'GitHub is behind or has diverged; refusing a non-fast-forward deployment'

STAMP="$(date -u +%Y%m%dT%H%M%SZ)"
MYSQL_CNF="$(mktemp "${TMPDIR:-/tmp}/myvivahai-mysql.XXXXXX")"
BACKUP_TMP="${BACKUP_DIR}/myvivahai-${STAMP}.sql.gz.tmp"
BACKUP_FILE="${BACKUP_DIR}/myvivahai-${STAMP}.sql.gz"
HEALTH_TMP=""
MAINTENANCE_ACTIVE=0

cleanup() {
    local status=$?
    trap - EXIT INT TERM
    if [[ "$MAINTENANCE_ACTIVE" == 1 ]]; then
        printf 'Deployment interrupted; attempting to bring the app out of maintenance mode.\n' >&2
        php artisan up >/dev/null || status=1
    fi
    [[ -z "$MYSQL_CNF" ]] || rm -f -- "$MYSQL_CNF"
    [[ -z "$BACKUP_TMP" ]] || rm -f -- "$BACKUP_TMP"
    [[ -z "$HEALTH_TMP" ]] || rm -f -- "$HEALTH_TMP"
    exit "$status"
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

# Write credentials only to a mode-600 temporary MySQL option file; they never
# appear in command arguments, logs, or terminal output.
php -r '
require "vendor/autoload.php";
$v = Dotenv\Dotenv::parse(file_get_contents(".env"));
$quote = static fn ($s) => str_replace(["\\", "\""], ["\\\\", "\\\""], (string) $s);
printf("[client]\nhost=\"%s\"\nport=%d\nuser=\"%s\"\npassword=\"%s\"\n", $quote($v["DB_HOST"]), (int) $v["DB_PORT"], $quote($v["DB_USERNAME"]), $quote($v["DB_PASSWORD"]));
' > "$MYSQL_CNF" || fail 'could not prepare protected database backup credentials'
chmod 600 "$MYSQL_CNF"
DATABASE_NAME="$(php -r 'require "vendor/autoload.php"; $v = Dotenv\Dotenv::parse(file_get_contents(".env")); echo $v["DB_DATABASE"];')"
[[ "$DATABASE_NAME" =~ ^[A-Za-z0-9_-]+$ ]] || fail 'DB_DATABASE contains unsupported characters'

printf 'Creating compressed database backup before deployment...\n'
mysqldump --defaults-extra-file="$MYSQL_CNF" --single-transaction --routines --triggers --default-character-set=utf8mb4 \
    "$DATABASE_NAME" | gzip -1 > "$BACKUP_TMP" || fail 'database backup failed; application was not placed in maintenance mode'
gzip -t "$BACKUP_TMP" || fail 'database backup archive did not validate'
[[ -s "$BACKUP_TMP" ]] || fail 'database backup is empty'
chmod 600 "$BACKUP_TMP"
mv -- "$BACKUP_TMP" "$BACKUP_FILE"
BACKUP_TMP=''
printf 'Backup ready: %s\n' "$BACKUP_FILE"

# Limit access to production environment values. Laravel/PHP runs as this account.
chmod 600 .env
php artisan down --retry=60
MAINTENANCE_ACTIVE=1

if [[ "$CURRENT_REVISION" != "$TARGET_REVISION" ]]; then
    git merge --ff-only "$TARGET_REVISION"
fi

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan db:seed --class=ProductionPlanSeeder --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan up
MAINTENANCE_ACTIVE=0

HEALTH_TMP="$(mktemp "${TMPDIR:-/tmp}/myvivahai-health.XXXXXX")"
HEALTH_OK=0
for attempt in 1 2 3 4 5; do
    if curl --fail --silent --show-error --max-time 15 "$HEALTH_URL" -o "$HEALTH_TMP" \
        && php -r '
$data = json_decode(file_get_contents($argv[1]), true);
if (($data["success"] ?? false) !== true || ($data["data"]["checks"]["database"] ?? false) !== true) exit(1);
' "$HEALTH_TMP"; then
        HEALTH_OK=1
        break
    fi
    sleep 2
done
[[ "$HEALTH_OK" == 1 ]] || fail 'production readiness check did not pass after five attempts'
printf 'Production readiness check passed (database ready).\n'

printf 'Deployment complete at %s (%s).\n' "$TARGET_REVISION" "$STAMP"
