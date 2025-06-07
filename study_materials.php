<?php
require_once 'config/database.php';

// Check if a specific subject_id is provided
// If yes, redirect to view_materials.php
if (isset($_GET['subject_id']) && !empty($_GET['subject_id'])) {
    $subject_id = (int)$_GET['subject_id'];
    header("Location: view_materials.php?subject_id=$subject_id");
    exit;
}

$branches = ['Computer Science', 'Information Technology', 'Electronics & Communication', 'Mechanical Engineering', 'Civil Engineering', 'Electrical Engineering'];
$semesters = range(1, 8);

// Get user preferences from cookie
$userPreferences = isset($_COOKIE['userPreferences']) ? json_decode($_COOKIE['userPreferences'], true) : null;

// Use preferences if available, otherwise use GET parameters
$selected_branch = isset($_GET['branch']) ? $_GET['branch'] : ($userPreferences ? $userPreferences['branch'] : '');
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : ($userPreferences ? $userPreferences['semester'] : '');

$subjects = [];
if ($selected_branch && $selected_semester) {
    $query = "SELECT * FROM subjects WHERE branch = :branch AND semester = :semester ORDER BY subject_name";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':branch', $selected_branch);
    $stmt->bindParam(':semester', $selected_semester);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Set SEO meta tags
$page_title = $selected_branch && $selected_semester
    ? "Study Materials for {$selected_branch} - Semester {$selected_semester} | NEXX Learning"
    : "Study Materials | NEXX Learning";
$page_description = $selected_branch && $selected_semester
    ? "Access comprehensive study materials for {$selected_branch} Semester {$selected_semester}. Download notes, PDFs, and resources for all subjects."
    : "Access comprehensive study materials for all engineering branches. Download notes, PDFs, and resources for your academic success.";

include 'includes/header.php';
?>

<div class="min-h-screen" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto p-6 pb-16">
        <!-- Page Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Study Materials</h1>
            <div class="w-24 h-1 bg-purple-600 mx-auto mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Access comprehensive study materials, notes, and resources for all subjects to enhance your learning experience.</p>
        </div>

        <div class="rounded-xl shadow-lg overflow-hidden border border-purple-200 mb-8">
            <div style="background-color: #0E0B1A;" class="px-6 py-6 rounded-t-xl">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-white mb-2">Find Study Materials</h2>
                    <p class="text-gray-300">Select your branch and semester to view available study materials</p>
                </div>
            </div>

            <div class="p-8 bg-white">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-5">
                        <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">Select Branch</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 appearance-none"
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
                    <div class="md:col-span-5">
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Select Semester</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 appearance-none"
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
                        <i class="fas fa-book-open text-purple-500"></i>
                    </span>
                </div>
            </div>

            <?php if (!empty($subjects)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="bg-white shadow-lg rounded-xl p-6 border border-purple-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center mb-5">
                                <div class="w-12 h-12 rounded-full gradient-bg flex items-center justify-center mr-3">
                                    <i class="fas fa-book-open text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($subject['subject_name']); ?></h3>
                                    <p class="text-gray-500 text-sm">
                                        <i class="fas fa-hashtag mr-1 text-purple-500"></i> <?php echo htmlspecialchars($subject['subject_code']); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4 mb-5">
                                <div class="flex items-center text-purple-700 mb-2">
                                    <i class="fas fa-graduation-cap mr-2"></i>
                                    <span class="font-medium"><?php echo htmlspecialchars($subject['branch']); ?></span>
                                </div>
                                <div class="flex items-center text-purple-700">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span class="font-medium">Semester <?php echo $subject['semester']; ?></span>
                                </div>
                            </div>

                            <a href="view_materials.php?subject_id=<?php echo $subject['id']; ?>"
                               class="block w-full gradient-bg text-white py-3 px-4 rounded-lg hover:opacity-90 transition duration-300 text-center font-medium">
                                <i class="fas fa-book-open mr-2"></i> View Materials
                            </a>
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
                        No subjects found for <?php echo htmlspecialchars($selected_branch); ?> - Semester <?php echo htmlspecialchars($selected_semester); ?>.
                    </p>
                    <button onclick="window.location.href='study_materials.php'" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Reset Filters
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Prompt to select branch and semester -->
            <div class="text-center py-16 mt-8 bg-white rounded-xl shadow-lg border border-purple-100">
                <div class="w-20 h-20 rounded-full gradient-bg mx-auto flex items-center justify-center mb-6">
                    <i class="fas fa-search text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Select Branch and Semester</h3>
                <p class="text-gray-600 max-w-md mx-auto">
                    Please select your branch and semester above to view available study materials.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>