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

// Get subject details
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

if (!$subject_id) {
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

// Get study materials
$query = "SELECT * FROM study_materials WHERE subject_id = :subject_id ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':subject_id', $subject_id);
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set SEO meta tags
$page_title = "Study Materials - " . htmlspecialchars($subject['subject_name']) . " | NEXX Learning";
$page_description = "Access comprehensive study materials, video lectures, and PDF resources for " . htmlspecialchars($subject['subject_name']) . ". Download notes and watch video lectures for better understanding.";

include 'includes/header.php';
?>

<div class="min-h-screen" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Study Materials</h1>
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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($subject['subject_name']); ?></span>
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
                            <i class="fas fa-book-open text-white"></i>
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

        <?php if (!empty($materials)): ?>
            <div class="space-y-6">
                <?php
                $currentUnit = 1;
                foreach ($materials as $material):
                ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-purple-100">
                        <!-- Unit Header -->
                        <div style="background-color: #0E0B1A;" class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-3">
                                    <span class="text-white font-bold"><?php echo $currentUnit; ?></span>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">
                                        <?php echo htmlspecialchars($material['title']); ?>
                                    </h2>
                                    <?php if ($material['description']): ?>
                                        <p class="mt-1 text-gray-300 text-sm">
                                            <?php echo htmlspecialchars($material['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Resources Grid -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- PDF Section -->
                                <div class="bg-purple-50 rounded-lg p-6 border border-purple-100">
                                    <div class="flex items-center mb-4">
                                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-file-pdf text-red-500"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900">Study Material (PDF)</h3>
                                    </div>
                                    <?php
                                    // Handle both JSON array and direct URL string
                                    $pdf_urls = [];
                                    if (!empty($material['file_path'])) {
                                        if (substr($material['file_path'], 0, 1) === '[') {
                                            // Try to decode as JSON
                                            $decoded = json_decode($material['file_path'], true);
                                            if (is_array($decoded)) {
                                                $pdf_urls = $decoded;
                                            }
                                        } else {
                                            // Treat as a single URL
                                            $pdf_urls = [$material['file_path']];
                                        }
                                    }

                                    if (!empty($pdf_urls)):
                                    ?>
                                        <div class="space-y-3">
                                            <?php foreach ($pdf_urls as $index => $pdf_url): ?>
                                                <?php if (!empty(trim($pdf_url))): ?>
                                                    <a href="<?php echo htmlspecialchars($pdf_url); ?>"
                                                       class="flex items-center p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition duration-300 border border-purple-100"
                                                       target="_blank">
                                                        <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center mr-3">
                                                            <i class="fas fa-download text-red-500"></i>
                                                        </div>
                                                        <span class="text-gray-700 font-medium">PDF Document <?php echo $index + 1; ?></span>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-gray-500 italic text-center py-4">No PDF materials available yet</p>
                                    <?php endif; ?>
                                </div>

                                <!-- Video Section -->
                                <div class="bg-blue-50 rounded-lg p-6 border border-blue-100">
                                    <div class="flex items-center mb-4">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-video text-blue-500"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900">Video Lectures</h3>
                                    </div>
                                    <?php
                                    // Handle both JSON array and direct URL string
                                    $video_urls = [];
                                    if (!empty($material['video_url'])) {
                                        if (substr($material['video_url'], 0, 1) === '[') {
                                            // Try to decode as JSON
                                            $decoded = json_decode($material['video_url'], true);
                                            if (is_array($decoded)) {
                                                $video_urls = $decoded;
                                            }
                                        } else {
                                            // Treat as a single URL
                                            $video_urls = [$material['video_url']];
                                        }
                                    }

                                    if (!empty($video_urls)):
                                    ?>
                                        <div class="space-y-3">
                                            <?php foreach ($video_urls as $index => $video_url): ?>
                                                <?php if (!empty(trim($video_url))): ?>
                                                    <a href="view_video.php?subject_id=<?php echo $subject_id; ?>&video_url=<?php echo urlencode($video_url); ?>"
                                                       class="flex items-center p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition duration-300 border border-blue-100"
                                                       target="_blank">
                                                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mr-3">
                                                            <i class="fas fa-play-circle text-blue-500"></i>
                                                        </div>
                                                        <span class="text-gray-700 font-medium">Video Lecture <?php echo $index + 1; ?></span>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-gray-500 italic text-center py-4">No video lectures available yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-6 text-sm text-gray-500 flex items-center">
                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center mr-2">
                                    <i class="far fa-clock text-gray-500"></i>
                                </div>
                                Added: <?php echo date('F j, Y', strtotime($material['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                <?php
                $currentUnit++;
                endforeach;
                ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16 mt-8 bg-white rounded-xl shadow-lg border border-purple-100">
                <div class="w-20 h-20 rounded-full gradient-bg mx-auto flex items-center justify-center mb-6">
                    <i class="fas fa-book-open text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Study Materials Available</h3>
                <p class="text-gray-600 max-w-md mx-auto">Study materials for this subject will be added soon. Please check back later.</p>
                <a href="study_materials.php" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Study Materials
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>