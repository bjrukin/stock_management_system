<script>
    // State management
    let isSubmitting = false;
    let mediaStream = null;
    let isCameraActive = false;
    let photoCaptured = false;
    let cameraPermissionDenied = false;

    // DOM elements
    const videoElement = document.getElementById('video');
    const canvasElement = document.getElementById('canvas');
    const photoPreview = document.getElementById('photoPreview');
    const biometricPhotoInput = document.getElementById('biometric_photo');
    const startCameraBtn = document.getElementById('start-camera');
    const clickPhotoBtn = document.getElementById('click-photo');
    const retakePhotoBtn = document.getElementById('retake-photo');
    const photoStatus = document.getElementById('photoStatus');
    const photoError = document.getElementById('photoError');
    const agreementCheckbox = document.getElementById('photoAgreement');
    const agreementError = document.getElementById('agreementError');
    const cameraError = document.getElementById('cameraError');
    const cameraLoading = document.getElementById('cameraLoading');
    const cameraInstructions = document.getElementById('cameraInstructions');
    const submitButton = document.getElementById('submitButton');
    const applicationForm = document.getElementById('applicationForm');

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        // Initially hide the capture photo button
        clickPhotoBtn.style.display = 'none';

        if (applicationForm) {
            console.log('Biometric photo script loaded, skipping individual submit listener.');
        }
    });

    // Enhanced Camera Permission Check Function
    async function checkCameraPermissions() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');

            if (videoDevices.length === 0) {
                return { hasCamera: false, permission: 'no-camera' };
            }

            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 1 },
                    height: { ideal: 1 }
                }
            });

            stream.getTracks().forEach(track => track.stop());
            return { hasCamera: true, permission: 'granted' };

        } catch (error) {
            if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                return { hasCamera: true, permission: 'denied' };
            } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                return { hasCamera: false, permission: 'no-camera' };
            } else {
                return { hasCamera: false, permission: 'unknown', error: error };
            }
        }
    }

    // Improved Start Camera Function
    startCameraBtn.addEventListener('click', async function () {
        try {
            // Show loading state
            cameraLoading.style.display = 'block';
            cameraError.style.display = 'none';
            startCameraBtn.disabled = true;
            startCameraBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';

            // Hide capture photo button during camera start
            clickPhotoBtn.style.display = 'none';
            clickPhotoBtn.disabled = true;

            // Check current permissions first
            const permissionCheck = await checkCameraPermissions();

            if (permissionCheck.permission === 'denied') {
                cameraPermissionDenied = true;
                showCameraDeniedError();
                return;
            }

            if (permissionCheck.permission === 'no-camera') {
                showNoCameraError();
                return;
            }

            // Show instructions before requesting camera
            cameraInstructions.style.display = 'block';

            // Request camera access
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: { ideal: 'user' },
                    frameRate: { ideal: 30 }
                },
                audio: false
            });

            // Success! Hide instructions
            cameraInstructions.style.display = 'none';
            cameraLoading.style.display = 'none';

            // Set video source
            videoElement.srcObject = mediaStream;
            videoElement.style.display = 'block';

            // Wait for video to load
            await new Promise((resolve) => {
                videoElement.onloadedmetadata = () => {
                    // Set canvas dimensions to match video
                    canvasElement.width = videoElement.videoWidth;
                    canvasElement.height = videoElement.videoHeight;
                    resolve();
                };
            });

            // IMPORTANT: Show and enable the capture photo button
            clickPhotoBtn.style.display = 'inline-block';
            clickPhotoBtn.disabled = false;

            // Update UI
            startCameraBtn.style.display = 'none';
            isCameraActive = true;
            photoPreview.style.display = 'none';
            cameraPermissionDenied = false;

            // Hide any error messages
            cameraError.style.display = 'none';

        } catch (error) {
            console.error('Camera error:', error);
            handleCameraError(error);
        }
    });

    // Handle Camera Error Function
    function handleCameraError(error) {
        cameraLoading.style.display = 'none';

        let errorMessage = '';

        switch (error.name) {
            case 'NotAllowedError':
            case 'PermissionDeniedError':
                cameraPermissionDenied = true;
                errorMessage = `
                    <i class="fas fa-ban fa-3x text-danger mb-2"></i>
                    <h5 class="text-danger mb-2">Camera Permission Denied</h5>
                    <p class="mb-2">You have denied camera access. To enable:</p>
                    <ol class="small mb-2 text-start">
                        <li><strong>Chrome/Edge:</strong> Click the camera icon (🚫) in address bar → Always allow</li>
                        <li><strong>Firefox:</strong> Click the camera icon in address bar → Allow</li>
                        <li><strong>Safari:</strong> Safari → Preferences → Websites → Camera → Allow</li>
                        <li>Or click the lock icon (🔒) next to the URL and adjust permissions</li>
                    </ol>
                    <p class="mb-0 text-warning">
                        <i class="fas fa-lightbulb"></i> After changing settings, refresh this page
                    </p>
                `;
                break;

            case 'NotFoundError':
            case 'DevicesNotFoundError':
                errorMessage = `
                    <i class="fas fa-video-slash fa-3x text-danger mb-2"></i>
                    <h5 class="text-danger mb-2">No Camera Found</h5>
                    <p class="mb-2">Please make sure:</p>
                    <ul class="small mb-2 text-start">
                        <li>A camera is connected to your device</li>
                        <li>No other app is using the camera</li>
                        <li>Camera drivers are properly installed</li>
                        <li>Try using a different browser or device</li>
                    </ul>
                `;
                break;

            case 'NotReadableError':
            case 'TrackStartError':
                errorMessage = `
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-2"></i>
                    <h5 class="text-warning mb-2">Camera in Use</h5>
                    <p>Your camera is being used by another application.</p>
                    <p class="small">Close other apps (Zoom, Teams, etc.) and try again.</p>
                `;
                break;

            default:
                errorMessage = `
                    <i class="fas fa-times-circle fa-3x text-danger mb-2"></i>
                    <h5 class="text-danger mb-2">Camera Error</h5>
                    <p>Unable to access camera. Please try:</p>
                    <ul class="small mb-2 text-start">
                        <li>Refreshing the page</li>
                        <li>Using a different browser</li>
                        <li>Checking your camera connection</li>
                    </ul>
                `;
        }

        // Display error
        cameraError.innerHTML = errorMessage;
        cameraError.style.display = 'block';
        cameraInstructions.style.display = 'none';

        // Reset start button
        startCameraBtn.disabled = false;
        startCameraBtn.innerHTML = '<i class="fas fa-camera"></i> Start Camera';
        startCameraBtn.style.display = 'inline-block';

        // Ensure capture photo button is hidden on error
        clickPhotoBtn.style.display = 'none';
        clickPhotoBtn.disabled = true;

        // Add retry button
        const retryBtn = document.createElement('button');
        retryBtn.className = 'btn btn-sm btn-primary mt-2';
        retryBtn.innerHTML = '<i class="fas fa-redo"></i> Try Again';
        retryBtn.onclick = () => {
            cameraError.style.display = 'none';
            startCameraBtn.click();
        };
        cameraError.appendChild(retryBtn);
    }

    // Show Camera Denied Error
    function showCameraDeniedError() {
        cameraLoading.style.display = 'none';
        cameraError.innerHTML = `
            <div class="text-center">
                <i class="fas fa-ban fa-3x text-danger mb-3"></i>
                <h4 class="text-danger mb-3">Camera Access Blocked</h4>
                <div class="alert alert-warning text-start mb-3">
                    <p><strong>Your browser has permanently blocked camera access for this site.</strong></p>
                    <p class="mb-2">To fix this:</p>
                    <ol class="mb-0">
                        <li><strong>Click the lock/camera icon</strong> in your browser's address bar</li>
                        <li>Change camera permission from "Block" to "Allow"</li>
                        <li>Refresh this page and try again</li>
                    </ol>
                </div>
                <button class="btn btn-primary" onclick="showPermissionInstructions()">
                    <i class="fas fa-question-circle"></i> Detailed Instructions
                </button>
            </div>
        `;
        cameraError.style.display = 'block';
        startCameraBtn.style.display = 'inline-block';
        startCameraBtn.disabled = false;
        startCameraBtn.innerHTML = '<i class="fas fa-camera"></i> Start Camera';

        // Hide capture photo button
        clickPhotoBtn.style.display = 'none';
        clickPhotoBtn.disabled = true;
    }

    // Show No Camera Error
    function showNoCameraError() {
        cameraLoading.style.display = 'none';
        cameraError.innerHTML = `
            <div class="text-center">
                <i class="fas fa-desktop fa-3x text-info mb-3"></i>
                <h4 class="text-info mb-3">No Camera Detected</h4>
                <p class="mb-3">Your device doesn't seem to have a camera or it's not accessible.</p>
                <div class="alert alert-info text-start mb-3">
                    <p class="mb-2"><strong>You need to:</strong></p>
                    <ul class="mb-0">
                        <li>Use a different device with a camera</li>
                        <li>Connect an external webcam</li>
                        <li>Try using a different browser</li>
                    </ul>
                </div>
                <button class="btn btn-primary" onclick="showCameraHelp()">
                    <i class="fas fa-question-circle"></i> Need Help?
                </button>
            </div>
        `;
        cameraError.style.display = 'block';
        startCameraBtn.style.display = 'inline-block';
        startCameraBtn.disabled = false;
        startCameraBtn.innerHTML = '<i class="fas fa-camera"></i> Start Camera';

        // Hide capture photo button
        clickPhotoBtn.style.display = 'none';
        clickPhotoBtn.disabled = true;
    }

    // Global functions for error handling
    window.showPermissionInstructions = function () {
        const instructions = `
            <div class="alert alert-info text-start">
                <h5><i class="fas fa-cog"></i> How to Enable Camera Permissions</h5>
                <hr>
                <h6><i class="fab fa-chrome"></i> Google Chrome / Microsoft Edge:</h6>
                <ol>
                    <li>Click the lock icon (🔒) in the address bar</li>
                    <li>Find "Camera" in the permissions list</li>
                    <li>Change from "Block" to "Allow"</li>
                    <li>Refresh the page</li>
                </ol>
                
                <h6><i class="fab fa-firefox"></i> Mozilla Firefox:</h6>
                <ol>
                    <li>Click the camera icon in the address bar</li>
                    <li>Select "Allow" for camera access</li>
                    <li>If not shown, go to Options → Privacy & Security → Permissions → Camera Settings</li>
                </ol>
                
                <h6><i class="fab fa-safari"></i> Apple Safari:</h6>
                <ol>
                    <li>Safari → Preferences → Websites → Camera</li>
                    <li>Find this website in the list</li>
                    <li>Change from "Deny" to "Allow"</li>
                    <li>Refresh the page</li>
                </ol>
            </div>
        `;

        cameraError.innerHTML = instructions;
    };

    window.showCameraHelp = function () {
        cameraError.innerHTML = `
            <div class="alert alert-info text-start">
                <h5><i class="fas fa-question-circle"></i> Camera Help</h5>
                <p>For biometric verification, you must use a camera-enabled device:</p>
                <ul>
                    <li><strong>Smartphone:</strong> Use Chrome or Safari browser</li>
                    <li><strong>Laptop/Desktop:</strong> Ensure webcam is connected</li>
                    <li><strong>Tablet:</strong> Most tablets have front cameras</li>
                </ul>
                <p class="mb-0"><strong>Note:</strong> Biometric photos must be taken live for security verification.</p>
            </div>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-redo"></i> Reload Page
            </button>
        `;
    };

    // Capture Photo Function
    clickPhotoBtn.addEventListener('click', function () {
        if (!isCameraActive) {
            alert('Please start camera first');
            return;
        }

        try {
            // Get canvas context
            const ctx = canvasElement.getContext('2d');

            // Set canvas to match video dimensions
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;

            // Draw video frame to canvas
            ctx.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            // Convert to base64 with medium quality to reduce payload size
            const imageData = canvasElement.toDataURL('image/jpeg', 0.5);

            // Store in hidden input
            biometricPhotoInput.value = imageData;

            // Show preview
            photoPreview.src = imageData;
            photoPreview.style.display = 'block';
            photoPreview.style.width = '100%';
            photoPreview.style.height = '100%';
            photoPreview.style.objectFit = 'cover';
            videoElement.style.display = 'none';

            // Hide face frame overlay when showing preview
            document.querySelector('.face-frame').style.display = 'none';

            // Update UI - Hide capture, show retake
            clickPhotoBtn.style.display = 'none';
            clickPhotoBtn.disabled = true;
            retakePhotoBtn.style.display = 'inline-block';

            // Update status
            photoStatus.innerHTML = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Photo captured successfully</span>';
            photoError.style.display = 'none';
            photoCaptured = true;

            // Stop camera stream
            if (mediaStream) {
                mediaStream.getTracks().forEach(track => track.stop());
                isCameraActive = false;
            }

        } catch (error) {
            console.error('Capture error:', error);
            alert('Error capturing photo. Please try again.');
        }
    });

    // Retake Photo Function
    retakePhotoBtn.addEventListener('click', function () {
        // Reset states
        photoCaptured = false;
        biometricPhotoInput.value = '';
        photoPreview.style.display = 'none';
        retakePhotoBtn.style.display = 'none';

        // Show face frame overlay again
        document.querySelector('.face-frame').style.display = 'block';

        // Reset camera
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
        videoElement.srcObject = null;

        // Hide photo status and error
        photoError.style.display = 'none';
        photoStatus.innerHTML = '<span class="badge badge-danger">Photo not captured</span>';

        // Show start camera button again
        startCameraBtn.style.display = 'inline-block';
        startCameraBtn.disabled = false;
        startCameraBtn.innerHTML = '<i class="fas fa-camera"></i> Start Camera';

        // Hide capture photo button
        clickPhotoBtn.style.display = 'none';
        clickPhotoBtn.disabled = true;

        // Reset camera state
        isCameraActive = false;

        // Hide any camera errors
        cameraError.style.display = 'none';
        cameraLoading.style.display = 'none';
        cameraInstructions.style.display = 'none';
    });

    // Agreement validation
    agreementCheckbox.addEventListener('change', function () {
        agreementError.style.display = this.checked ? 'none' : 'block';
    });

    // Page navigation warning
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (link && !isSubmitting && (photoCaptured || biometricPhotoInput.value)) {
            e.preventDefault();

            if (confirm('Warning: You have unsaved biometric photo data. Leaving this page will discard your captured photo. Do you want to leave?')) {
                window.location.href = link.href;
            }
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', (e) => {
        if (!isSubmitting && photoCaptured) {
            e.preventDefault();
            e.returnValue = 'You have unsaved biometric photo data. Are you sure you want to leave?';
            return e.returnValue;
        }
    });

    // Stop camera when leaving page
    window.addEventListener('pagehide', () => {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
        }
    });

</script>