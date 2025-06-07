<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="fixed w-full z-50 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="background-color: #0E0B1A;" class="rounded-full shadow-lg px-6 py-2">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="index.php" class="flex items-center">
                            <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-2">
                                <span class="text-xl font-bold text-white">N</span>
                            </div>
                            <span class="text-xl font-bold text-white">NEXX</span>
                        </a>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="flex items-center">
                    <div class="hidden sm:flex sm:space-x-1">
                        <a href="subjects.php"
                           class="<?php echo $current_page === 'subjects.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition duration-300">
                            <i class="fas fa-book mr-2"></i>
                            Subjects
                        </a>
                        <a href="study_materials.php"
                           class="<?php echo $current_page === 'study_materials.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition duration-300">
                            <i class="fas fa-file-alt mr-2"></i>
                            Study Materials
                        </a>
                        <a href="practicals.php"
                           class="<?php echo $current_page === 'practicals.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition duration-300">
                            <i class="fas fa-laptop-code mr-2"></i>
                            Practicals
                        </a>
                        <a href="previous_papers.php"
                           class="<?php echo $current_page === 'previous_papers.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition duration-300">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Previous Papers
                        </a>
                        <a href="syllabus.php"
                           class="<?php echo $current_page === 'syllabus.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition duration-300">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Syllabus
                        </a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button"
                            class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-full text-white hover:text-gray-200 hover:bg-purple-900 hover:bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            aria-controls="mobile-menu"
                            aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!-- Icon when menu is closed -->
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Icon when menu is open -->
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="mt-3 mx-4">
            <div style="background-color: #0E0B1A;" class="rounded-2xl shadow-lg p-4 space-y-2">
                <a href="subjects.php"
                   class="<?php echo $current_page === 'subjects.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> flex items-center px-4 py-3 rounded-xl text-base font-medium transition duration-300">
                    <div class="w-8 h-8 rounded-full gradient-bg flex items-center justify-center mr-3">
                        <i class="fas fa-book"></i>
                    </div>
                    Subjects
                </a>
                <a href="study_materials.php"
                   class="<?php echo $current_page === 'study_materials.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> flex items-center px-4 py-3 rounded-xl text-base font-medium transition duration-300">
                    <div class="w-8 h-8 rounded-full gradient-bg flex items-center justify-center mr-3">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    Study Materials
                </a>
                <a href="practicals.php"
                   class="<?php echo $current_page === 'practicals.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> flex items-center px-4 py-3 rounded-xl text-base font-medium transition duration-300">
                    <div class="w-8 h-8 rounded-full gradient-bg flex items-center justify-center mr-3">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    Practicals
                </a>
                <a href="previous_papers.php"
                   class="<?php echo $current_page === 'previous_papers.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> flex items-center px-4 py-3 rounded-xl text-base font-medium transition duration-300">
                    <div class="w-8 h-8 rounded-full gradient-bg flex items-center justify-center mr-3">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    Previous Papers
                </a>
                <a href="syllabus.php"
                   class="<?php echo $current_page === 'syllabus.php' ? 'bg-purple-900 bg-opacity-50 text-white' : 'text-gray-300 hover:bg-purple-900 hover:bg-opacity-30 hover:text-white'; ?> flex items-center px-4 py-3 rounded-xl text-base font-medium transition duration-300">
                    <div class="w-8 h-8 rounded-full gradient-bg flex items-center justify-center mr-3">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    Syllabus
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Add padding to account for fixed header -->
<div class="pt-20"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIconOpen = mobileMenuButton.querySelector('svg:not(.hidden)');
    const menuIconClose = mobileMenuButton.querySelector('svg.hidden');

    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
        menuIconOpen.classList.toggle('hidden');
        menuIconClose.classList.toggle('hidden');
    });
});
</script>