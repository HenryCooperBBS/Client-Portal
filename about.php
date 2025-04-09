<?php
session_start();
require_once 'includes/db.php';
?>

<?php include 'templates/header.php'; ?>

<div class="bg-gradient-to-r from-blue-50 to-purple-100 min-h-screen p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-10">
        <h1 class="text-4xl font-extrabold text-center mb-8 text-blue-800 drop-shadow">
            👋 Hey, I'm Henry Cooper
        </h1>

        <p class="text-gray-700 text-lg mb-6 text-center leading-relaxed">
            I'm a passionate <span class="font-bold text-blue-600">Full Stack Developer</span> with a strong focus on building dynamic, scalable, and user-friendly web applications.
            I thrive on turning ideas into reality through clean, efficient code and modern design principles.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10">
            <!-- Skills -->
            <div>
                <h2 class="text-2xl font-bold text-blue-700 mb-4">💻 Technical Skills</h2>
                <ul class="list-disc list-inside text-gray-600 space-y-2">
                    <li><span class="font-semibold text-gray-800">Languages:</span> PHP, JavaScript, SQL, HTML5, CSS3</li>
                    <li><span class="font-semibold text-gray-800">Frameworks:</span> TailwindCSS, Alpine.js</li>
                    <li><span class="font-semibold text-gray-800">Tools:</span> Git, Linux (Ubuntu VPS), Nginx, MySQL</li>
                    <li><span class="font-semibold text-gray-800">Other:</span> Responsive Design, API Development, Server Management</li>
                </ul>
            </div>

            <!-- Goals -->
            <div>
                <h2 class="text-2xl font-bold text-blue-700 mb-4">🚀 Current Goals</h2>
                <ul class="list-disc list-inside text-gray-600 space-y-2">
                    <li>Expand my portfolio with real-world projects</li>
                    <li>Master modern JavaScript frameworks (Vue.js, React)</li>
                    <li>Contribute to open-source projects</li>
                    <li>Grow into a backend development specialist</li>
                </ul>
            </div>
        </div>

        <!-- CV Button -->
        <div class="flex justify-center mt-12">
            <a href="uploads/Henry Cooper - CV.pdf" target="_blank"
               class="bg-gradient-to-r from-green-400 to-blue-500 hover:from-green-500 hover:to-blue-600 text-white font-bold py-3 px-6 rounded-full text-lg transition">
                📄 View My CV
            </a>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
