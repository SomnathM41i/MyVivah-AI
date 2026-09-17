# MyVivahAI — API Integration (Client Dashboard)

## Overview

After purchasing the Real-Time Chat service, the client platform owner accesses the **API Integration** section in the MyVivahAI dashboard. This section guides the client through connecting their external platform's backend to MyVivahAI so that the chat widget can identify users, fetch user details, and search for users.

**Note:** The client platform configures MyVivahAI to call *the client's own* external APIs. These are the client's provided endpoints, not MyVivahAI's endpoints.

---

## Integration Steps

### Step 1: Open API Integration

After subscription activation, the client sees the "API Integration" item in the dashboard sidebar under the Chat service.

### Step 2: View Required API Capabilities

The dashboard lists the required external API capabilities for the Real-Time Chat widget:

1. **User Identity / Current User API** — Required
2. **User Details API** — Required
3. **User Search API** — Required
4. **Chat Permission API** — Optional (only if the client wants to control who can talk to whom)

See [api-contract.md](api-contract.md) for full contract details.

### Step 3: Read API Documentation

Each capability is expandable and shows:

- Purpose of the capability
- Expected request method and URL pattern
- Expected request headers
- Expected request body/query parameters
- Expected response structure
- Field mapping explanation
- Example request and response
- Common error cases

### Step 4: Configure API Endpoints

For each capability, the client provides:

| Field | Description |
|---|---|
| Endpoint URL | The URL where the external API is accessible |
| Authentication Method | None / Bearer Token / API Key / Custom Header |
| Credentials | Token/key value(s), if applicable |
| Additional Headers | Key-value pairs sent with every request |
| Timeout | Optional custom request timeout |
| Request Method | GET, POST, etc. (usually prefilled but can be changed) |

### Step 5: Configure Authentication

Supported authentication options (open decision on final list):

- **None** — the endpoint is publicly accessible (discouraged but allowed for testing)
- **Bearer Token** — static token sent in `Authorization: Bearer <token>`
- **API Key** — sent as a header or query parameter
- **Custom Header** — arbitrary header name/value

Credentials are stored encrypted (see [security.md](security.md)).

### Step 6: Configure Request Headers (if required)

Clients may add arbitrary headers, e.g.:

```
X-Platform-ID: myplatform-123
Accept: application/json
```

### Step 7: Map Response Fields

If the external API returns data in a non-standard shape, the client can map fields to MyVivahAI's expected schema.

Example mapping for User Details API:

| MyVivahAI Field | External API Field Path | Auto-detected |
|---|---|---|
| external_user_id | data.id | Yes |
| name | data.full_name | Yes (best-effort) |
| email | data.email | Yes |
| phone | data.phone | Yes |
| profile_photo_url | data.profile_picture | No |

**Open decision:** Whether field mapping should be required or optional. Recommended: implement smart field auto-detection with manual override.

### Step 8: Test APIs

The dashboard has a "Test" button per capability and a "Test All" button.

When tested, MyVivahAI:

1. Sends a request to the configured endpoint with test parameters.
2. Records request/response.
3. Validates the response against expected format.
4. Shows the result in a side panel.

### Step 9: View Request and Response Results

Results show:

- HTTP status code
- Response body (formatted)
- Time taken
- Whether the response passed validation
- Specific validation errors (missing fields, wrong types, etc.)

### Step 10: Fix Validation Errors

The dashboard surfaces actionable errors, e.g.:

| Error | Meaning |
|---|---|
| `MISSING_FIELD: id` | Response does not contain the `id` field |
| `WRONG_FORMAT: email` | `email` field present but not a valid email |
| `AUTH_FAILED` | Authentication failed (401/403) |
| `ENDPOINT_UNREACHABLE` | Connection failed or timeout |
| `INVALID_USER_ID` | Reference user ID not found by the external API |
| `FIELD_TYPE_MISMATCH` | A field exists but has an unexpected type |

### Step 11: Re-test the Integration

The client fixes the configuration and re-runs tests until passing.

### Step 12: Activate the Integration

Once all required capabilities pass validation, the client clicks "Activate Integration." The integration status changes to `ACTIVE`.

---

## Integration States

| State | Meaning |
|---|---|
| NOT_CONFIGURED | No endpoints configured yet |
| CONFIGURED | Endpoints saved but not tested |
| TESTING | Tests are running |
| TEST_FAILED | One or more tests failed |
| TEST_PASSED | All required tests passed but not activated |
| ACTIVE | All tests passed and integration activated by the client |
| SUSPENDED | Integration suspended (admin or system) |

---

## The Dashboard Helps Identify

| Problem | How Dashboard Surfaces It |
|---|---|
| Missing parameters | Validation errors on test run |
| Incorrect endpoint URLs | Connection errors / 404 |
| Invalid credentials | 401/403 plus auth failure message |
| Invalid response formats | Format validation errors |
| Missing response fields | Missing field errors |
| Authentication failures | Auth failure result |
| Connection errors | Timeout/unreachable errors |
| Invalid user IDs | API returns "user not found" errors |
| Incorrect field mappings | Data type mismatch or empty mapped values |

---

## Security Considerations

- API credentials are encrypted at rest.
- Credentials are masked in UI (only last 4 chars visible).
- API test executions are rate-limited.
- Test requests include a request ID for auditing.
- Responses are truncated if too large.
- No secrets are stored in browser-stored configuration or widget scripts.

---

## Open Decisions

1. Whether the client can supply a sample/test user ID for testing purposes.
2. Whether the "Auth Header" for API calls should immediately reveal a 401 on test.
3. Whether MyVivahAI should do a "dry run" against production-like test endpoints.
4. Rate limits on API test buttons per minute/day.
5. Whether API test logs are retained permanently or pruned.