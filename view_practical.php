<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get subject_id and practical_id from URL
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$practical_id = isset($_GET['practical_id']) ? (int)$_GET['practical_id'] : 0;

if (!$subject_id || !$practical_id) {
    header('Location: practicals.php');
    exit;
}

try {
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

    // Get practical details with questions
    $stmt = $conn->prepare("
        SELECT p.*,
               GROUP_CONCAT(
                   JSON_OBJECT(
                       'question_number', pq.question_number,
                       'question_text', pq.question_text,
                       'description', pq.description,
                       'code_solution', pq.code_solution,
                       'code_lang', pq.code_lang,
                       'question_image', pq.question_image,
                       'description_image', pq.description_image,
                       'solution_image', pq.solution_image
                   )
               ) as questions
        FROM practicals p
        LEFT JOIN practical_questions pq ON p.id = pq.practical_id
        WHERE p.id = :practical_id AND p.subject_id = :subject_id
        GROUP BY p.id
    ");
    $stmt->bindParam(':practical_id', $practical_id);
    $stmt->bindParam(':subject_id', $subject_id);
    $stmt->execute();
    $practical = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$practical) {
        header('Location: practicals.php?subject_id=' . $subject_id);
        exit;
    }

    // Process questions data
    if ($practical['questions']) {
        $practical['questions'] = json_decode('[' . $practical['questions'] . ']', true);
    } else {
        $practical['questions'] = [];
    }
} catch(PDOException $e) {
    $error = "Failed to load practical data: " . $e->getMessage();
    error_log("Database Error in view_practical.php: " . $e->getMessage());
}

include 'includes/header.php';
?>

<!-- Add Prism.js CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism-tomorrow.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" />

<style>
    /* Override Prism.js theme colors to match VS Code dark theme */
    code[class*="language-"],
    pre[class*="language-"] {
        color: #d4d4d4;
        background: #1e1e1e;
        text-shadow: none;
        font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
        font-size: 14px;
        line-height: 1.5;
    }

    .token.comment {
        color: #6a9955;
    }

    .token.keyword {
        color: #c586c0;
    }

    .token.string {
        color: #ce9178;
    }

    .token.number {
        color: #b5cea8;
    }

    .token.operator {
        color: #d4d4d4;
    }

    .token.preprocessor,
    .token.macro {
        color: #569cd6;
    }

    .token.function {
        color: #dcdcaa;
    }

    .token.class-name {
        color: #4ec9b0;
    }

    .token.variable {
        color: #9cdcfe;
    }

    /* Line numbers styling */
    .line-numbers .line-numbers-rows {
        border-right: 1px solid #404040;
    }

    .line-numbers-rows > span:before {
        color: #858585;
    }

    /* Code block container styling */
    .bg-gray-800 {
        background-color: #1e1e1e !important;
    }

    pre[class*="language-"].line-numbers {
        padding-left: 3.8em;
    }

    /* Copy button styling */
    .copy-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 3px 10px;
    }

    .copy-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
    }

    /* Image size constraints */
    .practical-image {
        max-width: 500px;
        max-height: 300px;
        width: auto;
        height: auto;
        object-fit: contain;
        margin: 0 auto;
        display: block;
    }

    .practical-image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 1rem 0;
    }
</style>

<div class="min-h-screen" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Practical Details</h1>
            <div class="w-24 h-1 bg-purple-600 mx-auto mb-6"></div>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-purple-200">
                <!-- Header Section -->
                <div style="background-color: #0E0B1A;" class="px-6 py-6 rounded-t-xl">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-4 md:mb-0">
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center mr-3">
                                    <i class="fas fa-laptop-code text-white"></i>
                                </div>
                                <h1 class="text-2xl font-bold text-white">
                                    <?php echo htmlspecialchars($practical['title']); ?>
                                </h1>
                            </div>
                            <p class="text-gray-300 flex flex-wrap items-center gap-4">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-book mr-2"></i>
                                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-graduation-cap mr-2"></i>
                                    <?php echo htmlspecialchars($subject['branch']); ?>
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Semester <?php echo $subject['semester']; ?>
                                </span>
                            </p>
                        </div>
                        <a href="practicals.php?subject_id=<?php echo $subject_id; ?>"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Practicals
                        </a>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="p-8">
                    <?php if (!empty($practical['questions'])): ?>
                        <div class="space-y-8">
                            <?php foreach ($practical['questions'] as $question): ?>
                                <div class="bg-white rounded-xl p-6 shadow-md border border-purple-100 transform transition duration-300 hover:shadow-lg">
                                    <div class="flex items-center mb-5">
                                        <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-full gradient-bg text-white">
                                            <span class="text-lg font-bold"><?php echo $question['question_number']; ?></span>
                                        </div>
                                        <h3 class="ml-4 text-xl font-bold text-gray-900">
                                            Question <?php echo $question['question_number']; ?>
                                        </h3>
                                    </div>

                                    <div class="prose max-w-none">
                                        <div class="bg-purple-50 rounded-lg p-5 mb-6">
                                            <p class="text-gray-700">
                                                <?php echo nl2br(htmlspecialchars($question['question_text'])); ?>
                                            </p>

                                            <?php if (!empty($question['question_image'])): ?>
                                                <div class="practical-image-container mt-4">
                                                    <img src="<?php echo str_replace('\\', '/', htmlspecialchars($question['question_image'])); ?>"
                                                         alt="Question Image"
                                                         class="practical-image rounded-lg shadow-md">
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($question['description'])): ?>
                                            <div class="bg-blue-50 rounded-lg p-5 mb-6 border border-blue-100">
                                                <h4 class="text-blue-800 font-medium mb-3 flex items-center">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    Description
                                                </h4>
                                                <p class="text-blue-700">
                                                    <?php echo nl2br(htmlspecialchars($question['description'])); ?>
                                                </p>

                                                <?php if (!empty($question['description_image'])): ?>
                                                    <div class="practical-image-container mt-4">
                                                        <img src="<?php echo str_replace('\\', '/', htmlspecialchars($question['description_image'])); ?>"
                                                             alt="Description Image"
                                                             class="practical-image rounded-lg shadow-md">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($question['code_solution']) || !empty($question['solution_image'])): ?>
                                            <div class="bg-gray-800 rounded-lg p-5 mb-4 relative shadow-lg">
                                                <div class="flex justify-between items-center mb-3">
                                                    <h4 class="text-gray-200 font-medium flex items-center">
                                                        <i class="fas fa-code mr-2"></i>
                                                        Solution
                                                    </h4>
                                                    <?php if (!empty($question['code_solution'])): ?>
                                                        <button onclick="copyCode(this)" class="copy-btn text-gray-400 hover:text-white transition-colors duration-200 rounded px-3 py-1">
                                                            <i class="fas fa-copy mr-1"></i>
                                                            <span class="copy-text">Copy</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if (!empty($question['code_solution'])): ?>
                                                    <?php
                                                    $lang_map = [
                                                        'cpp' => 'cpp',
                                                        'c' => 'c',
                                                        'java' => 'java',
                                                        'python' => 'python',
                                                        'javascript' => 'javascript',
                                                        'php' => 'php',
                                                        'sql' => 'sql',
                                                        'html' => 'markup',
                                                        'css' => 'css'
                                                    ];
                                                    $lang_class = $lang_map[$question['code_lang'] ?? 'cpp'] ?? 'cpp';
                                                    ?>
                                                    <pre class="line-numbers rounded-lg"><code class="language-<?php echo htmlspecialchars($lang_class); ?>"><?php echo htmlspecialchars($question['code_solution']); ?></code></pre>
                                                <?php endif; ?>

                                                <?php if (!empty($question['solution_image'])): ?>
                                                    <div class="practical-image-container bg-white p-3 rounded-lg mt-4">
                                                        <img src="<?php echo str_replace('\\', '/', htmlspecialchars($question['solution_image'])); ?>"
                                                             alt="Solution Image"
                                                             class="practical-image rounded-lg">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
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
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Questions Available</h3>
                            <p class="text-gray-600 max-w-md mx-auto">No questions have been added for this practical yet.</p>
                            <a href="practicals.php?subject_id=<?php echo $subject_id; ?>" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white gradient-bg hover:opacity-90 transition duration-300">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Practicals
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Add Prism.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/plugins/line-numbers/prism-line-numbers.min.js"></script>

<!-- Load core language components -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-clike.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-c.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-cpp.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-java.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-sql.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-markup.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-css.min.js"></script>

<!-- Load PHP with its dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-markup-templating.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-php.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Prism after all components are loaded
        if (typeof Prism !== 'undefined') {
            Prism.highlightAll();
        }
    });

    function copyCode(button) {
        const codeBlock = button.parentElement.nextElementSibling;
        const code = codeBlock.querySelector('code').textContent;

        navigator.clipboard.writeText(code).then(() => {
            const copyText = button.querySelector('.copy-text');
            const originalText = copyText.textContent;
            copyText.textContent = 'Copied!';

            setTimeout(() => {
                copyText.textContent = originalText;
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy code: ', err);
        });
    }
</script>