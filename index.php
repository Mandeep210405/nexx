<?php
require_once 'config/database.php';
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg {
            /*  */
            background: #0E0B1A;
        }
        .gradient-bg{
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            background: #8C4AEA;
        }
        .feature-card {
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body style="background-color: #f8f9fa;">
    <!-- Welcome Modal -->
    <div id="welcomeModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div style="background-color: #0E0B1A;" class="rounded-lg p-8 max-w-md w-full mx-4 border border-purple-500 shadow-xl">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white">Welcome to NEXX Learning!</h2>
                <p class="text-gray-300 mt-3">Please select your branch and semester to get personalized content.</p>
            </div>

            <form id="preferencesForm" class="space-y-6">
                <div>
                    <label for="branchSelect" class="block text-sm font-medium text-gray-200 mb-2">Select Branch</label>
                    <select id="branchSelect" class="w-full px-4 py-3 bg-gray-900 border border-purple-500 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white" required>
                        <option value="" class="bg-gray-900">Choose branch</option>
                        <option value="Computer Science" class="bg-gray-900">Computer Science</option>
                        <option value="Information Technology" class="bg-gray-900">Information Technology</option>
                        <option value="Electronics & Communication" class="bg-gray-900">Electronics & Communication</option>
                        <option value="Mechanical Engineering" class="bg-gray-900">Mechanical Engineering</option>
                        <option value="Civil Engineering" class="bg-gray-900">Civil Engineering</option>
                        <option value="Electrical Engineering" class="bg-gray-900">Electrical Engineering</option>
                    </select>
                </div>
                <div>
                    <label for="semesterSelect" class="block text-sm font-medium text-gray-200 mb-2">Select Semester</label>
                    <select id="semesterSelect" class="w-full px-4 py-3 bg-gray-900 border border-purple-500 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white" required>
                        <option value="" class="bg-gray-900">Choose semester</option>
                        <?php for($i = 1; $i <= 8; $i++): ?>
                            <option value="<?php echo $i; ?>" class="bg-gray-900">Semester <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="button" onclick="saveUserPreferences()" class="w-full gradient-bg text-white py-3 px-4 rounded-lg hover:opacity-90 transition duration-300 font-bold text-lg mt-4">
                    Save Preferences
                </button>
            </form>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="bg py-12 md:min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-white text-center md:text-left md:w-1/2">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-4 md:mb-6">Welcome to NEXX</h1>
                    <p class="text-lg sm:text-xl md:text-2xl mb-6 md:mb-8 opacity-90">Your one-stop destination for quality educational resources</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="subjects.php" class="bg-white text-purple-600 px-6 sm:px-8 py-3 rounded-lg font-bold hover:bg-opacity-90 transition duration-300">
                            Explore Subjects
                        </a>
                    </div>
                </div>
                <div class="mt-8 md:mt-0 md:w-1/2 flex justify-center">
                    <img src="./assets/images/16.png" alt="Hero Image" class="w-4/5 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20" style="background-color: #0E0B1A;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center text-white mb-6">WHY CHOOSE NEXX?</h2>
            <p class="text-center text-gray-300 max-w-3xl mx-auto mb-16">All your study needs in one place - notes, lectures, papers, and more. Designed for students, powered by simplicity and smart learning tools.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-gray-900 bg-opacity-50 p-8 rounded-xl shadow-lg hover:shadow-xl border border-purple-800">
                    <div class="text-purple-500 mb-4">
                        <i class="fas fa-book-open text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-white">ALL-IN-ONE HUB</h3>
                    <p class="text-gray-300">Access syllabus, study materials, video lectures, and more.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-gray-900 bg-opacity-50 p-8 rounded-xl shadow-lg hover:shadow-xl border border-purple-800">
                    <div class="text-purple-500 mb-4">
                        <i class="fas fa-bolt text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-white">INSTANT & FREE</h3>
                    <p class="text-gray-300">No fees, no sign-ups. Get what you need instantly, without any barriers.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-gray-900 bg-opacity-50 p-8 rounded-xl shadow-lg hover:shadow-xl border border-purple-800">
                    <div class="text-purple-500 mb-4">
                        <i class="fas fa-file-pdf text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-white">PREVIOUS YEAR PAPERS</h3>
                    <p class="text-gray-300">Use flashcards, quizzes, and revision tools to study smarter, not harder.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20" style="background-color: #0E0B1A;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-white mb-6">OUR IMPACT</h2>
            <p class="text-center text-gray-300 max-w-2xl mx-auto mb-12">Empowering students with comprehensive resources and tools to excel in their academic journey.</p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 text-center">
                <div class="p-6 md:p-8 rounded-xl border border-purple-500 bg-purple-900 bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-800 bg-opacity-50 mb-4 mx-auto">
                        <i class="fas fa-book-open text-2xl text-purple-300"></i>
                    </div>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">1000+</div>
                    <div class="text-gray-300 font-medium">Study Materials</div>
                </div>

                <div class="p-6 md:p-8 rounded-xl border border-purple-500 bg-purple-900 bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-800 bg-opacity-50 mb-4 mx-auto">
                        <i class="fas fa-laptop-code text-2xl text-purple-300"></i>
                    </div>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">500+</div>
                    <div class="text-gray-300 font-medium">Practical Guides</div>
                </div>

                <div class="p-6 md:p-8 rounded-xl border border-purple-500 bg-purple-900 bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-800 bg-opacity-50 mb-4 mx-auto">
                        <i class="fas fa-graduation-cap text-2xl text-purple-300"></i>
                    </div>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">50+</div>
                    <div class="text-gray-300 font-medium">Subjects</div>
                </div>

                <div class="p-6 md:p-8 rounded-xl border border-purple-500 bg-purple-900 bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-800 bg-opacity-50 mb-4 mx-auto">
                        <i class="fas fa-users text-2xl text-purple-300"></i>
                    </div>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">10K+</div>
                    <div class="text-gray-300 font-medium">Active Students</div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
    // Check if user preferences exist in localStorage
    if (!localStorage.getItem('userPreferences')) {
        // Show the welcome modal
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('welcomeModal').classList.remove('hidden');
            document.getElementById('welcomeModal').classList.add('flex');
        });
    }

    function saveUserPreferences() {
        const branch = document.getElementById('branchSelect').value;
        const semester = document.getElementById('semesterSelect').value;

        if (branch && semester) {
            // Save preferences to localStorage
            localStorage.setItem('userPreferences', JSON.stringify({
                branch: branch,
                semester: semester
            }));

            // Save preferences to cookie
            const preferences = JSON.stringify({
                branch: branch,
                semester: semester
            });
            document.cookie = `userPreferences=${preferences}; path=/; max-age=31536000`; // Cookie expires in 1 year

            // Hide the modal
            document.getElementById('welcomeModal').classList.add('hidden');
            document.getElementById('welcomeModal').classList.remove('flex');

            // Reload the page to apply filters
            window.location.reload();
        }
    }
    </script>
</body>
</html>