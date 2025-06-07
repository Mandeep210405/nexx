document.addEventListener('DOMContentLoaded', function() {
    // Check if user preferences exist in localStorage
    if (!localStorage.getItem('userPreferences')) {
        // Show the welcome modal
        const modal = new bootstrap.Modal(document.getElementById('welcomeModal'));
        modal.show();
    }
});

function saveUserPreferences() {
    const branch = document.getElementById('branchSelect').value;
    const semester = document.getElementById('semesterSelect').value;

    if (branch && semester) {
        // Save preferences to localStorage
        localStorage.setItem('userPreferences', JSON.stringify({
            branch: branch,
            semester: semester
        }));

        // Hide the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('welcomeModal'));
        modal.hide();

        // Reload the page to apply filters
        window.location.reload();
    }
} 