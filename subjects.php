<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Set SEO meta tags
$page_title = "Subjects | NEXX Learning";
$page_description = "Browse and access study materials, practicals, and resources for various engineering subjects.";

// Define available branches and semesters
$branches = ['Computer Science', 'Information Technology', 'Electronics & Communication', 'Mechanical Engineering', 'Civil Engineering', 'Electrical Engineering'];
$semesters = range(1, 8);

// Get user preferences from cookie
$userPreferences = isset($_COOKIE['userPreferences']) ? json_decode($_COOKIE['userPreferences'], true) : null;

// Use preferences if available, otherwise use GET parameters
$selected_branch = isset($_GET['branch']) ? $_GET['branch'] : ($userPreferences ? $userPreferences['branch'] : '');
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : ($userPreferences ? $userPreferences['semester'] : '');

try {
    // Build the query based on selected branch and semester
    $query = "
        SELECT s.*,
               COUNT(DISTINCT p.id) as practical_count,
               COUNT(DISTINCT m.id) as material_count
        FROM subjects s
        LEFT JOIN practicals p ON s.id = p.subject_id
        LEFT JOIN study_materials m ON s.id = m.subject_id
        WHERE 1=1
    ";

    $params = [];

    if (!empty($selected_branch)) {
        $query .= " AND s.branch = :branch";
        $params[':branch'] = $selected_branch;
    }

    if (!empty($selected_semester)) {
        $query .= " AND s.semester = :semester";
        $params[':semester'] = $selected_semester;
    }

    $query .= " GROUP BY s.id ORDER BY s.subject_name";

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = "Failed to load data: " . $e->getMessage();
    error_log("Database Error: " . $e->getMessage());
}

include 'includes/header.php';
?>

<div class="min-h-screen" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto p-6 pb-16">
        <!-- Page Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Subjects</h1>
            <div class="w-24 h-1 bg-purple-600 mx-auto mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Browse through our comprehensive collection of subjects and access study materials, practicals, and resources tailored to your academic needs.</p>
        </div>

        <div class="rounded-xl shadow-lg overflow-hidden border border-purple-200">
            <div style="background-color: #0E0B1A;" class="px-6 py-6 rounded-t-xl">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-white mb-2">Find Your Subjects</h2>
                    <p class="text-gray-300">Select your branch and semester to view available subjects</p>
                </div>
            </div>

            <div class="p-8 bg-white">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-5">
                        <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">Select Branch</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 appearance-none"
                                    id="branch" name="branch">
                                <option value="">All Branches</option>
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
                    <div class="md:col-span-5">
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Select Semester</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 appearance-none"
                                    id="semester" name="semester">
                                <option value="">All Semesters</option>
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
        <div class="relative py-8">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center">
                <span style="background-color: #f8f9fa;" class="px-4 text-sm text-gray-500">
                    <i class="fas fa-book text-purple-500"></i>
                </span>
            </div>
        </div>

        <?php if (!empty($subjects)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($subjects as $subject): ?>
                    <div class="bg-white shadow-lg rounded-xl p-6 border border-purple-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-3">
                                <i class="fas fa-book-open text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">
                                <?php echo htmlspecialchars($subject['subject_name']); ?>
                            </h3>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-3 mb-4">
                            <p class="text-gray-700 mb-1">
                                <span class="font-medium text-purple-700">Code:</span>
                                <?php echo htmlspecialchars($subject['subject_code']); ?>
                            </p>
                            <p class="text-gray-700 mb-1">
                                <span class="font-medium text-purple-700">Branch:</span>
                                <?php echo htmlspecialchars($subject['branch']); ?>
                            </p>
                            <p class="text-gray-700">
                                <span class="font-medium text-purple-700">Semester:</span>
                                <?php echo $subject['semester']; ?>
                            </p>
                        </div>

                        <div class="flex justify-between text-sm text-gray-600 mb-5 bg-gray-50 rounded-lg p-3">
                            <span class="flex items-center">
                                <i class="fas fa-book mr-2 text-purple-500"></i>
                                <span class="font-medium"><?php echo $subject['material_count']; ?> Materials</span>
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-code mr-2 text-purple-500"></i>
                                <span class="font-medium"><?php echo $subject['practical_count']; ?> Practicals</span>
                            </span>
                        </div>

                        <button onclick="openSubjectOptions(<?php echo $subject['id']; ?>, '<?php echo htmlspecialchars($subject['subject_name']); ?>')"
                                class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-300">
                            <i class="fas fa-eye mr-2"></i>
                            View Resources
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16 mt-8 bg-white rounded-xl shadow-lg border border-purple-100">
                <div class="w-20 h-20 rounded-full gradient-bg mx-auto flex items-center justify-center mb-6">
                    <i class="fas fa-book-open text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Subjects Found</h3>
                <p class="text-gray-600 max-w-md mx-auto">
                    <?php if ($selected_branch && $selected_semester): ?>
                        No subjects found for <?php echo htmlspecialchars($selected_branch); ?> - Semester <?php echo htmlspecialchars($selected_semester); ?>.
                    <?php elseif ($selected_branch): ?>
                        No subjects found for <?php echo htmlspecialchars($selected_branch); ?>.
                    <?php elseif ($selected_semester): ?>
                        No subjects found for Semester <?php echo htmlspecialchars($selected_semester); ?>.
                    <?php else: ?>
                        There are no subjects available at the moment.
                    <?php endif; ?>
                </p>
                <button onclick="window.location.href='subjects.php'" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Reset Filters
                </button>
            </div>
        <?php endif; ?>

        <!-- Subject Options Modal -->
        <div id="subjectOptionsModal" class="fixed inset-0 bg-black bg-opacity-70 hidden overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
            <div class="relative top-20 mx-auto p-6 w-96 shadow-xl rounded-xl border border-purple-500" style="background-color: #0E0B1A;">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white" id="modalSubjectName"></h3>
                    <button onclick="closeSubjectOptions()" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-gray-300 mb-6">Select a resource type to explore:</p>
                <div class="space-y-4">
                    <a href="#" id="studyMaterialsLink"
                       class="flex items-center px-4 py-3 bg-purple-900 bg-opacity-50 text-white rounded-lg hover:bg-opacity-70 transition duration-300 border border-purple-500">
                        <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="font-medium">Study Materials</span>
                    </a>
                    <a href="#" id="practicalsLink"
                       class="flex items-center px-4 py-3 bg-purple-900 bg-opacity-50 text-white rounded-lg hover:bg-opacity-70 transition duration-300 border border-purple-500">
                        <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-3">
                            <i class="fas fa-code"></i>
                        </div>
                        <span class="font-medium">Practicals</span>
                    </a>
                    <a href="#" id="previousPapersLink"
                       class="flex items-center px-4 py-3 bg-purple-900 bg-opacity-50 text-white rounded-lg hover:bg-opacity-70 transition duration-300 border border-purple-500">
                        <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-3">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <span class="font-medium">Previous Year Papers</span>
                    </a>
                    <a href="#" id="syllabusLink"
                       class="flex items-center px-4 py-3 bg-purple-900 bg-opacity-50 text-white rounded-lg hover:bg-opacity-70 transition duration-300 border border-purple-500">
                        <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-3">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <span class="font-medium">Syllabus</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Subject search functionality
    const searchInput = document.getElementById('subjectSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const cards = document.querySelectorAll('.subject-card');

            cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const code = card.querySelector('p.text-gray-600').textContent.toLowerCase();
                const branch = card.querySelector('p:nth-of-type(2)').textContent.toLowerCase();

                if (title.includes(searchText) || code.includes(searchText) || branch.includes(searchText)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});

function openSubjectOptions(subjectId, subjectName) {
    const modal = document.getElementById('subjectOptionsModal');

    // Update modal title
    document.getElementById('modalSubjectName').textContent = subjectName;

    // Update links with subject ID
    const studyMaterialsLink = document.getElementById('studyMaterialsLink');
    const practicalsLink = document.getElementById('practicalsLink');
    const previousPapersLink = document.getElementById('previousPapersLink');
    const syllabusLink = document.getElementById('syllabusLink');

    studyMaterialsLink.href = `study_materials.php?subject_id=${subjectId}`;
    practicalsLink.href = `practicals.php?subject_id=${subjectId}`;
    previousPapersLink.href = `previous_papers.php?subject_id=${subjectId}`;
    syllabusLink.href = `syllabus.php?subject_id=${subjectId}`;

    // Add click handlers to close modal after navigation
    studyMaterialsLink.onclick = () => closeSubjectOptions();
    practicalsLink.onclick = () => closeSubjectOptions();
    previousPapersLink.onclick = () => closeSubjectOptions();
    syllabusLink.onclick = () => closeSubjectOptions();

    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
}

function closeSubjectOptions() {
    const modal = document.getElementById('subjectOptionsModal');
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Restore scrolling
}

// Close modal when clicking outside
document.getElementById('subjectOptionsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSubjectOptions();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSubjectOptions();
    }
});
</script>

<?php include 'includes/footer.php'; ?>