<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Set SEO meta tags
$page_title = "Previous Year Papers | NEXX Learning";
$page_description = "Access previous year question papers for various engineering subjects.";

$branches = ['Computer Science', 'Information Technology', 'Electronics & Communication', 'Mechanical Engineering', 'Civil Engineering', 'Electrical Engineering'];
$semesters = range(1, 8);

// Get user preferences from cookies
$userPreferences = isset($_COOKIE['userPreferences']) ? json_decode($_COOKIE['userPreferences'], true) : null;
$selected_branch = isset($_GET['branch']) ? $_GET['branch'] : ($userPreferences ? $userPreferences['branch'] : '');
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : ($userPreferences ? $userPreferences['semester'] : '');
$selected_subject = isset($_GET['subject']) ? $_GET['subject'] : ($userPreferences ? $userPreferences['subject'] : '');

// Save preferences to cookies if they're being changed
if (isset($_GET['branch']) && isset($_GET['semester'])) {
    $preferences = json_encode([
        'branch' => $_GET['branch'],
        'semester' => $_GET['semester'],
        'subject' => $_GET['subject'] ?? ''
    ]);
    setcookie('userPreferences', $preferences, time() + (86400 * 30), "/"); // 30 days
}

// Get available subjects for the selected branch and semester
$subjects = [];
if ($selected_branch && $selected_semester) {
    $subjectsQuery = "SELECT DISTINCT subject_name, subject_code FROM previous_papers
                     WHERE branch = :branch AND semester = :semester
                     ORDER BY subject_name";
    $stmt = $conn->prepare($subjectsQuery);
    $stmt->bindParam(':branch', $selected_branch);
    $stmt->bindParam(':semester', $selected_semester);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$papers = [];
if ($selected_branch && $selected_semester) {
    $query = "SELECT * FROM previous_papers WHERE branch = :branch AND semester = :semester";
    if ($selected_subject) {
        $query .= " AND subject_name = :subject";
    }
    $query .= " ORDER BY exam_year DESC, exam_session";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':branch', $selected_branch);
    $stmt->bindParam(':semester', $selected_semester);
    if ($selected_subject) {
        $stmt->bindParam(':subject', $selected_subject);
    }
    $stmt->execute();
    $papers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>

<div class="min-h-screen" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Previous Year Papers</h1>
            <div class="w-24 h-1 bg-purple-600 mx-auto mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Access previous year question papers to prepare for your exams and understand the exam pattern and important topics.</p>
        </div>

        <!-- Selection Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-purple-200 mb-8">
            <div style="background-color: #0E0B1A;" class="px-6 py-6 rounded-t-xl">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-white mb-2">Find Papers</h2>
                    <p class="text-gray-300">Select your branch and semester to view available papers</p>
                </div>
            </div>

            <div class="p-8 bg-white">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6" id="searchForm">
                    <div class="md:col-span-4">
                        <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">Select Branch</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 rounded-lg border border-purple-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 appearance-none"
                                    id="branch" name="branch" required>
                                <option value="">Choose Branch</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?php echo htmlspecialchars($branch); ?>"
                                            <?php echo $selected_branch === $branch ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($branch); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-purple-600">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Select Semester</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 rounded-lg border border-purple-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 appearance-none"
                                    id="semester" name="semester" required>
                                <option value="">Choose Semester</option>
                                <?php foreach ($semesters as $sem): ?>
                                    <option value="<?php echo $sem; ?>"
                                            <?php echo $selected_semester == $sem ? 'selected' : ''; ?>>
                                        Semester <?php echo $sem; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-purple-600">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Select Subject</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 rounded-lg border border-purple-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 appearance-none"
                                    id="subject" name="subject">
                                <option value="">All Subjects</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo htmlspecialchars($subject['subject_name']); ?>"
                                            <?php echo $selected_subject === $subject['subject_name'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-purple-600">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="submit" class="w-full gradient-bg text-white py-3 px-4 rounded-lg hover:opacity-90 transition duration-300 flex items-center justify-center font-medium">
                            <i class="fas fa-search mr-2"></i>
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section Divider -->
        <?php if ($selected_branch && $selected_semester): ?>
            <div class="relative py-8">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span style="background-color: #f8f9fa;" class="px-4 text-sm text-gray-500">
                        <i class="fas fa-file-alt text-purple-500"></i>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Papers Grid -->
        <?php if (!empty($papers)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($papers as $paper): ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-purple-100 transform transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-full gradient-bg text-white mr-4">
                                    <i class="fas fa-file-alt text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        <?php echo htmlspecialchars($paper['subject_name']); ?>
                                    </h3>
                                    <p class="text-gray-600">
                                        <i class="fas fa-hashtag text-purple-500 mr-1"></i>
                                        <?php echo htmlspecialchars($paper['subject_code']); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4 mb-5">
                                <div class="flex items-center text-purple-700 mb-2">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span class="font-medium">Year: <?php echo htmlspecialchars($paper['exam_year']); ?></span>
                                </div>
                                <div class="flex items-center text-purple-700">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span class="font-medium">Session: <?php echo htmlspecialchars($paper['exam_session']); ?></span>
                                </div>
                            </div>

                            <?php
                            // Clean up URL if it's in JSON format
                            $file_path = $paper['file_path'];
                            if (substr($file_path, 0, 1) === '[') {
                                $decoded = json_decode($file_path, true);
                                if (is_array($decoded) && !empty($decoded)) {
                                    $file_path = $decoded[0];
                                }
                            }
                            ?>
                            <a href="<?php echo htmlspecialchars($file_path); ?>"
                               class="inline-flex items-center w-full justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300"
                               target="_blank">
                                <i class="fas fa-download mr-2"></i>
                                Download Paper
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($selected_branch && $selected_semester): ?>
            <div class="text-center py-16 mt-8 bg-white rounded-xl shadow-lg border border-purple-100">
                <div class="w-20 h-20 rounded-full gradient-bg mx-auto flex items-center justify-center mb-6">
                    <i class="fas fa-file-alt text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Papers Found</h3>
                <p class="text-gray-600 max-w-md mx-auto">
                    No previous year papers found for <?php echo htmlspecialchars($selected_branch); ?> -
                    Semester <?php echo htmlspecialchars($selected_semester); ?>
                    <?php if ($selected_subject): ?>
                        - <?php echo htmlspecialchars($selected_subject); ?>
                    <?php endif; ?>.
                </p>
                <button onclick="window.location.href='previous_papers.php'" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Reset Filters
                </button>
            </div>
        <?php else: ?>
            <!-- Prompt to select branch and semester -->
            <div class="text-center py-16 mt-8 bg-white rounded-xl shadow-lg border border-purple-100">
                <div class="w-20 h-20 rounded-full gradient-bg mx-auto flex items-center justify-center mb-6">
                    <i class="fas fa-search text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Select Branch and Semester</h3>
                <p class="text-gray-600 max-w-md mx-auto">
                    Please select your branch and semester above to view available previous year papers.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchSelect = document.getElementById('branch');
    const semesterSelect = document.getElementById('semester');
    const subjectSelect = document.getElementById('subject');

    // Function to load subjects
    function loadSubjects() {
        const branch = branchSelect.value;
        const semester = semesterSelect.value;

        if (branch && semester) {
            // Clear current options
            subjectSelect.innerHTML = '<option value="">All Subjects</option>';

            // Make AJAX request to get subjects
            fetch(`ajax/get_subjects.php?branch=${encodeURIComponent(branch)}&semester=${encodeURIComponent(semester)}`)
                .then(response => response.json())
                .then(subjects => {
                    subjects.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.subject_name;
                        option.textContent = subject.subject_name;
                        subjectSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading subjects:', error));
        }
    }

    // Add event listeners
    branchSelect.addEventListener('change', loadSubjects);
    semesterSelect.addEventListener('change', loadSubjects);

    // Check if we have saved preferences but no GET parameters
    if (<?php echo json_encode($selected_branch && $selected_semester && !isset($_GET['branch'])); ?>) {
        // Submit the form automatically
        document.getElementById('searchForm').submit();
    }
});
</script>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>