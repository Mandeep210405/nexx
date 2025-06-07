<?php
require_once 'config/database.php';

// Function to convert YouTube URL to embed URL
function getYoutubeEmbedUrl($url) {
    $videoId = '';

    // Check for various YouTube URL formats
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
        $videoId = $matches[1];
    } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $matches)) {
        $videoId = $matches[1];
    } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $matches)) {
        $videoId = $matches[1];
    } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
        $videoId = $matches[1];
    }

    if ($videoId) {
        return 'https://www.youtube.com/embed/' . $videoId;
    }

    return false;
}

// Get parameters
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$video_url = isset($_GET['video_url']) ? urldecode($_GET['video_url']) : '';

if (!$subject_id || !$video_url) {
    header('Location: study_materials.php');
    exit;
}

// Get subject details
$query = "SELECT * FROM subjects WHERE id = :subject_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':subject_id', $subject_id);
$stmt->execute();
$subject = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subject) {
    header('Location: study_materials.php');
    exit;
}

// Get the embed URL
$embed_url = getYoutubeEmbedUrl($video_url);

if (!$embed_url) {
    header('Location: view_materials.php?subject_id=' . $subject_id);
    exit;
}

// Set SEO meta tags
$page_title = "Video Lecture - " . htmlspecialchars($subject['subject_name']) . " | NEXX Learning";
$page_description = "Watch video lectures for " . htmlspecialchars($subject['subject_name']) . ". Comprehensive video tutorials and explanations for better understanding.";

include 'includes/header.php';
?>

<div class="min-h-screen" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Video Lecture</h1>
            <div class="w-24 h-1 bg-purple-600 mx-auto mb-6"></div>
        </div>

        <!-- Breadcrumb Navigation -->
        <nav class="flex mb-6 bg-white p-3 rounded-lg shadow-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="study_materials.php" class="text-gray-700 hover:text-purple-600 inline-flex items-center">
                        <i class="fas fa-book mr-2 text-purple-500"></i>
                        Study Materials
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="view_materials.php?subject_id=<?php echo $subject_id; ?>" class="text-gray-700 hover:text-purple-600">
                            <?php echo htmlspecialchars($subject['subject_name']); ?>
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-700 font-medium">Video Lecture</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Subject Header -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-purple-200 mb-8">
            <div style="background-color: #0E0B1A;" class="px-6 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full gradient-bg flex items-center justify-center mr-3">
                            <i class="fas fa-video text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-1">
                                <?php echo htmlspecialchars($subject['subject_name']); ?>
                            </h1>
                            <p class="text-gray-300">
                                <i class="fas fa-hashtag mr-1"></i>
                                <?php echo htmlspecialchars($subject['subject_code']); ?>
                            </p>
                        </div>
                    </div>
                    <?php if (isset($subject['description'])): ?>
                        <p class="mt-4 md:mt-0 text-gray-300 max-w-md">
                            <?php echo htmlspecialchars($subject['description']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Video Player Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-purple-200 mb-8">
            <div style="background-color: #0E0B1A;" class="px-6 py-4 flex items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                    <i class="fas fa-play text-white"></i>
                </div>
                <h2 class="text-xl font-bold text-white">Video Lecture</h2>
            </div>
            <div class="p-6">
                <div class="relative pb-[56.25%] h-0">
                    <iframe
                        src="<?php echo htmlspecialchars($embed_url); ?>"
                        class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-center">
            <a href="view_materials.php?subject_id=<?php echo $subject_id; ?>"
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Materials
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>