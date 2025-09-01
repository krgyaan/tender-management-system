<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Take Photos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.css') }}">
    <style>
        body {
            background: #faf8f6;
        }


        .navbar {
            background: linear-gradient(135deg, #110703, #3a322d);
        }

        .navbar-brand img {
            height: 55px;
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        /* Card */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(38, 38, 38, 0.2);
            animation: fadeInUp 0.8s ease;
        }

        /* Form labels */
        .form-label {
            font-weight: 600;
            color: #383838;
        }

        /* Input and textarea */
        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #464646;
            box-shadow: 0 0 10px rgba(35, 34, 33, 0.25);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(90deg, #ff6a00, #ff9500);
            border: none;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 106, 0, 0.3);
        }

        .btn-outline-secondary {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: #ffefe0;
            border-color: #ff6a00;
            color: #ff6a00;
        }

        /* Title */
        .form-title {
            font-weight: 700;
            color: #252524;
            animation: fadeInDown 1s ease;
        }

        /* Footer */
        footer {
            background: linear-gradient(90deg, #ff6a00, #ff9500);
            color: white;
            padding: 15px 0;
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        .camera-card {
            max-width: 420px;
            margin: 24px auto;
            border-radius: 18px;
            box-shadow: 0 6px 28px rgba(50, 50, 50, 0.11);
            padding: 28px 20px 24px 20px;
            background: #fff;
        }

        .photos-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 16px;
            justify-content: center;
        }

        .photo-thumb {
            position: relative;
            width: 120px;
            height: 90px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(30, 30, 30, 0.07);
            background: #f6f5f2;
            transition: box-shadow 0.2s;
        }

        .photo-thumb:hover {
            box-shadow: 0 4px 16px rgba(30, 30, 30, 0.13);
        }

        .remove-btn {
            position: absolute;
            top: 2px;
            right: 4px;
            background: rgba(0, 0, 0, 0.67);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            line-height: 20px;
            z-index: 10;
            opacity: 0.85;
        }

        .remove-btn:hover {
            background: #ff4d4d;
            opacity: 1;
        }

        .webcam-wrap {
            width: 100%;
            max-width: 350px;
            height: 240px;
            border-radius: 12px;
            overflow: hidden;
            background: #eaeaea;
            margin: auto;
            border: 2px solid #ececec;
            box-shadow: 0 2px 8px rgba(30, 30, 30, 0.08);
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #110703, #3a322d);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="https://volksenergie.in/">
                <img src="https://volksenergie.in/wp-content/uploads/2024/09/VE_Logo-Final-Black_BG_Artboard-1.png"
                    alt="Volks Energie Logo" style="height:55px;">
            </a>
        </div>
    </nav>

    @include('partials.messages')
    <div class="camera-card">

        <h4 class="mb-3 text-center">Take Photos</h4>

        <!-- Camera live preview -->
        <div class="d-flex flex-column align-items-center mb-3">
            <div class="webcam-wrap mb-2">
                <video id="cameraStream" autoplay playsinline></video>
            </div>
            <button type="button" onclick="takeSnapshot()" class="btn btn-primary btn-md px-4 shadow-sm">Capture
                Photo</button>
        </div>

        <form method="POST" action="{{ route('service_visit.public.storePhotos') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value={{ $type }} name="type">
            <input type="hidden" value={{ $complaintId }} name="complaintId">
            <!-- Photo thumbnails with remove buttons -->
            <div class="photos-preview mb-3" id="photosPreview"></div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary btn-md px-4 me-2" id="submitBtn" disabled>Save
                    Photos</button>
                <a href="{{ url()->previous() }}" class="btn btn-md btn-outline-secondary px-4">Back</a>
            </div>
        </form>
    </div>

    <canvas id="snapshotCanvas" style="display:none;"></canvas>

    <footer class="text-center" style="  position: fixed; bottom: 0; left: 0; right:0; width:100%">
        <div class="container">
            <p>&copy; {{ date('Y') }} Volks Energie. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        const video = document.getElementById('cameraStream');
        const canvas = document.getElementById('snapshotCanvas');
        const preview = document.getElementById('photosPreview');
        const submitBtn = document.getElementById('submitBtn');
        let photos = [];

        // Start camera with back-facing mode
        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: {
                    ideal: "environment"
                }
            },
            audio: false
        }).then(stream => {
            video.srcObject = stream;
        }).catch(err => {
            alert("Camera access failed: " + err);
        });

        // Capture snapshot
        function takeSnapshot() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUri = canvas.toDataURL("image/jpeg");
            photos.push(dataUri);
            updatePreviews();
        }

        // Remove photo by index
        function removePhoto(idx) {
            photos.splice(idx, 1);
            updatePreviews();
        }

        // Update previews + hidden inputs
        function updatePreviews() {
            preview.innerHTML = '';
            document.querySelectorAll('.photo-hidden').forEach(e => e.remove());

            photos.forEach((src, idx) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'photo-thumb position-relative mx-1 mb-1';
                wrapper.innerHTML = `
                    <button type="button" class="remove-btn" onclick="removePhoto(${idx})">&times;</button>
                    <img src="${src}" class="img-fluid" style="width:100%; height:100%; object-fit:cover;"/>
                `;
                preview.appendChild(wrapper);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'captured_images[]';
                input.value = src;
                input.className = 'photo-hidden';
                document.querySelector('form').appendChild(input);
            });

            submitBtn.disabled = photos.length === 0;
        }
    </script>
</body>

</html>
