<?php
// Shared access-control helpers for the PIAT app.
//
// Roles (stored in auth_users.role and mirrored into $_SESSION['user_role']):
//   admin  - full access, sees all BAs
//   user   - full access, scoped to their own BA
//   viewer - read-only, sees all BAs (like admin) but cannot add/edit/delete
//
// NOTE: the caller must already have an active session before using these.

function current_user_role()
{
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'user';
}

function is_viewer()
{
    return current_user_role() === 'viewer';
}

// Viewer accounts have view-only rights. Sees everything an admin sees.
function is_admin_view()
{
    return (isset($_SESSION['user_name']) && $_SESSION['user_name'] === 'admin') || is_viewer();
}

// Call this at the top of any endpoint that creates, edits or deletes data
// to block write access for view-only accounts.
function deny_if_viewer()
{
    if (is_viewer()) {
        http_response_code(403);
        die('Access denied: your account has view-only permission.');
    }
}
