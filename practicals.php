<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Set SEO meta tags
$page_title = "Practicals | NEXX Learning";
$page_description = "Access practical assignments, coding exercises, and hands-on learning materials for various engineering subjects.";

// Define available branches and semesters
$branches = ['Computer Science', 'Information Technology', 'Electronics & Communication', 'Mechanical Engineering', 'Civil Engineering', 'Electrical Engineering'];
$semesters = range(1, 8);

// Get user preferences from cookie
$userPreferences = isset($_COOKIE['userPreferences']) ? json_decode($_COOKIE['userPreferences'], true) : null;

// Use preferences if available, otherwise use GET parameters
$selected_branch = isset($_GET['branch']) ? $_GET['branch'] : ($userPreferences ? $userPreferences['branch'] : '');
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : ($userPreferences ? $userPreferences['semester'] : '');

try {
    // If subject_id is selected, show practicals for that subject
    if (isset($_GET['subject_id'])) {
        $subject_id = (int)$_GET['subject_id'];

        // Get subject details
        $stmt = $conn->prepare("
            SELECT s.*
            FROM subjects s
            WHERE s.id = :subject_id
        ");
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->execute();
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$subject) {
            header('Location: practicals.php');
            exit;
        }

        // Get practicals for this subject
        $stmt = $conn->prepare("
            SELECT p.*,
                   COUNT(q.id) as question_count
            FROM practicals p
            LEFT JOIN practical_questions q ON p.id = q.practical_id
            WHERE p.subject_id = :subject_id
            GROUP BY p.id
            ORDER BY p.practical_number
        ");
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->execute();
        $practicals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $show_practicals = true;
    } else {
        // Build the query based on selected branch and semester
        $query = "
            SELECT s.*,
                   COUNT(p.id) as practical_count
            FROM subjects s
            LEFT JOIN practicals p ON s.id = p.subject_id
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
    }
} catch(PDOException $e) {
    $error = "Failed to load data: " . $e->getMessage();
    error_log("Database Error in practicals.php: " . $e->getMessage());
}

include 'includes/header.php';
?>

<div class="min-h-screen" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto p-6 pb-16">
        <?php if (!isset($_GET['subject_id'])): ?>
            <!-- Page Header -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold mb-4">Practicals</h1>
                <div class="w-24 h-1 bg-purple-600 mx-auto mb-6"></div>
                <p class="text-gray-600 max-w-2xl mx-auto">Access hands-on practical assignments, coding exercises, and laboratory experiments to enhance your learning experience.</p>
            </div>

            <div class="rounded-xl shadow-lg overflow-hidden border border-purple-200 mb-8">
                <div style="background-color: #0E0B1A;" class="px-6 py-6 rounded-t-xl">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-white mb-2">Find Practicals</h2>
                        <p class="text-gray-300">Select your branch and semester to view available practicals</p>
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
                        <i class="fas fa-laptop-code text-purple-500"></i>
                    </span>
                </div>
            </div>

            <?php if (!empty($subjects)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="bg-white shadow-lg rounded-xl p-6 border border-purple-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center mb-5">
                                <div class="w-12 h-12 rounded-full gradient-bg flex items-center justify-center mr-3">
                                    <i class="fas fa-laptop-code text-white text-lg"></i>
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

                            <div class="flex items-center bg-gray-50 rounded-lg p-3 mb-5">
                                <i class="fas fa-code mr-2 text-purple-500 text-lg"></i>
                                <span class="font-medium text-gray-700"><?php echo $subject['practical_count']; ?> Practicals</span>
                            </div>

                            <a href="practicals.php?subject_id=<?php echo $subject['id']; ?>"
                               class="block w-full gradient-bg text-white py-3 px-4 rounded-lg hover:opacity-90 transition duration-300 text-center font-medium">
                                <i class="fas fa-eye mr-2"></i>
                                View Practicals
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16 mt-8 bg-white rounded-xl shadow-lg border border-purple-100">
                    <div class="w-20 h-20 rounded-full gradient-bg mx-auto flex items-center justify-center mb-6">
                        <i class="fas fa-laptop-code text-white text-3xl"></i>
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
                    <button onclick="window.location.href='practicals.php'" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Reset Filters
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Page Header for Subject -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold mb-4">Practicals</h1>
                <div class="w-24 h-1 bg-purple-600 mx-auto mb-6"></div>
            </div>

            <!-- Display practicals for selected subject -->
            <div class="bg-white shadow-lg overflow-hidden rounded-xl border border-purple-200">
                <div style="background-color: #0E0B1A;" class="px-6 py-6 rounded-t-xl">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-2">
                                <?php echo htmlspecialchars($subject['subject_name']); ?>
                            </h1>
                            <p class="text-gray-300">
                                <span class="inline-flex items-center mr-4">
                                    <i class="fas fa-graduation-cap mr-2"></i>
                                    <?php echo htmlspecialchars($subject['branch']); ?>
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Semester <?php echo $subject['semester']; ?>
                                </span>
                            </p>
                        </div>
                        <a href="practicals.php"
                           class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-white text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Selection
                        </a>
                    </div>
                </div>

                <div class="p-8">
                    <?php if (isset($practicals) && !empty($practicals)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($practicals as $practical): ?>
                                <div class="bg-white shadow-lg rounded-xl p-6 border border-purple-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex items-center mb-4">
                                        <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-3">
                                            <span class="text-white font-bold"><?php echo $practical['practical_number']; ?></span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900">
                                            <?php echo htmlspecialchars($practical['title']); ?>
                                        </h3>
                                    </div>

                                    <div class="bg-purple-50 rounded-lg p-4 mb-5">
                                        <div class="flex items-center text-purple-700">
                                            <i class="fas fa-question-circle mr-2"></i>
                                            <span class="font-medium"><?php echo $practical['question_count']; ?> Questions</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <a href="view_practical.php?subject_id=<?php echo $subject_id; ?>&practical_id=<?php echo $practical['id']; ?>"
                                           class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-white gradient-bg hover:opacity-90 transition duration-300">
                                            <i class="fas fa-eye mr-2"></i>
                                            View Details
                                        </a>
                                        <?php if (!empty($practical['file_path'])): ?>
                                            <a href="<?php echo htmlspecialchars($practical['file_path']); ?>"
                                               class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-purple-300 text-sm font-medium text-purple-700 bg-white hover:bg-purple-50 rounded-lg transition duration-300"
                                               target="_blank">
                                                <i class="fas fa-download mr-2"></i>
                                                Download
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 bg-white rounded-xl shadow-inner">
                            <div class="w-20 h-20 rounded-full gradient-bg mx-auto flex items-center justify-center mb-6">
                                <i class="fas fa-code text-white text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Practicals Available</h3>
                            <p class="text-gray-600 max-w-md mx-auto">No practicals have been added for this subject yet.</p>
                            <a href="practicals.php" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Subjects
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>