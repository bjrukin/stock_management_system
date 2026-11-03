@if($level_id != 4)
<div class="col-6">
    <div class="alert alert-info mt-1">
        <i class="fas fa-info-circle"></i> Please capture your
        photo
        for
        biometric verification
    </div>

    <div class="text-center mb-3">
        <div class="camera-container">
            <div class="camera-box">
                <video id="video" autoplay playsinline></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <!-- Face frame overlay -->
                <div class="face-frame">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800">
                        <defs>
                            <style>
                            .corner {
                                stroke: #007bff;
                                stroke-width: 25;
                                stroke-linecap: round;
                                fill: none;
                            }

                            .guide-oval {
                                stroke: #007bff;
                                stroke-width: 16;
                                fill: none;
                            }
                            </style>
                        </defs>

                        <path class="corner" d="M140 170 L140 110 L200 110" />
                        <path class="corner" d="M600 110 L660 110 L660 170" />
                        <path class="corner" d="M660 630 L660 690 L600 690" />
                        <path class="corner" d="M200 690 L140 690 L140 630" />
                        <ellipse class="guide-oval" cx="400" cy="350" rx="160" ry="200" />
                    </svg>
                </div>

                <img id="photoPreview" class="preview-image" alt="Captured Photo" style="display: none;">

                <!-- Camera Error Message -->
                <div id="cameraError" class="text-center p-3" style="display: none;">
                    <i class="fas fa-video-slash fa-3x text-danger mb-2"></i>
                    <p class="text-danger mb-1">Camera not
                        accessible
                    </p>
                    <small class="text-muted">Please check
                        camera
                        permissions</small>
                </div>

                <!-- Camera Loading -->
                <div id="cameraLoading" class="text-center p-3" style="display: none;">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-2"></i>
                    <p class="text-primary">Initializing
                        camera...
                    </p>
                </div>
            </div>

            <div class="camera-controls">
                <button type="button" id="start-camera" class="btn btn-primary">
                    <i class="fas fa-camera"></i> Start Camera
                </button>
                <button type="button" id="click-photo" class="btn btn-success" disabled>
                    <i class="fas fa-camera-retro"></i> Capture
                    Photo
                </button>
                <button type="button" id="retake-photo" class="btn btn-warning" style="display: none;">
                    <i class="fas fa-redo"></i> Retake
                </button>

                <!-- Alternative: Upload Photo Button -->
                <button type="button" id="upload-photo-bn" class="btn btn-info" style="display: none;">
                    <i class="fas fa-upload"></i> Upload Photo
                    Instead
                </button>
                <input type="file" id="upload-photo" accept="image/*" capture="user" style="display: none;">
            </div>

            <div class="photo-status" id="photoStatus">
                <span class="badge badge-danger">Photo not
                    captured</span>
            </div>

            <input type="hidden" id="biometric_photo" name="biometric_photo" value="" required>
            <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">

            <div class="alert alert-danger mt-2" id="photoError" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                Please
                capture your biometric photo before submitting
            </div>

            <!-- Camera Permission Instructions -->
            <div class="alert alert-info mt-2" id="cameraInstructions" style="display: none;">
                <h6><i class="fas fa-info-circle"></i> Camera
                    Permission
                    Required</h6>
                <ol class="small mb-0">
                    <li>Click the "Allow" button when the
                        browser
                        asks
                        for camera permission</li>
                    <li>If denied, click the camera icon in your
                        browser's address bar and allow access
                    </li>
                    <li>Make sure no other application is using
                        your
                        camera</li>
                </ol>
            </div>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" checked required>
            <label class="form-check-label" for="photoAgreement">
                <strong>आवेदकको सहमति:</strong> म सहमत छु कि मैले
                माथि
                उल्लेखित सेल्फी फोटो सम्बन्धी निर्देशनहरू पढी बुझेको
                छु
                र
                दर्ता प्रक्रियामा अपलोड गरिएको सेल्फी फोटो मेरो
                आफ्नै हो
                तथा
                दर्ता गर्ने समयमा खिचिएको हो।
            </label>
        </div>
    </div>
</div>
@endif