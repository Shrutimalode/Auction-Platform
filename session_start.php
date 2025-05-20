<?php
if (session_status() === PHP_SESSION_NONE) {
    if (isset($_GET['tabKey'])) {
        session_name('PHPSESSID_' . $_GET['tabKey']); // Use unique session name for each tab
    }
    session_start();
}
?>

<script>
    // Assign a unique key for this tab if not already assigned
    if (!sessionStorage.getItem('tabKey')) {
        const tabKey = Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem('tabKey', tabKey);
    }

    const tabKey = sessionStorage.getItem('tabKey');

    // Append the tabKey to all form actions and links dynamically
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form, a').forEach(element => {
            if (element.tagName === 'FORM' && !element.action.includes('tabKey')) {
                element.action += `?tabKey=${tabKey}`;
            }
            if (element.tagName === 'A' && !element.href.includes('tabKey')) {
                element.href += (element.href.includes('?') ? '&' : '?') + `tabKey=${tabKey}`;
            }
        });
    });
</script>
