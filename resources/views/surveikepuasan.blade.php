@extends('layouts.home')
@section('content')
    <style>
        .survey-question {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            background-color: #fff;
            transition: border-color .15s ease-in-out;
        }

        .survey-question.answered {
            border-color: #198754;
        }

        .survey-question-title {
            font-weight: 600;
            color: #212529;
            margin-bottom: .9rem;
        }

        .survey-question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.6rem;
            height: 1.6rem;
            border-radius: 50%;
            background-color: #0d6efd;
            color: #fff;
            font-size: .8rem;
            margin-right: .5rem;
        }

        .survey-options {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .survey-options .btn {
            font-size: .85rem;
        }

        #survey-progress-label {
            font-size: .85rem;
        }
    </style>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h2>Survey Kepuasan</h2>
            </div>
            <div class="card-body">
                <form id="data-diri-form" action="{{ url('surveikepuasansimpan') }}" method="POST">
                    @csrf
                    @error('jawaban')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div class="modal fade" id="turnstileModal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center p-4">
                                    <h5 class="mb-3">Memeriksa keamanan koneksi...</h5>
                                    <div class="d-flex justify-content-center">
                                        <div id="turnstile-widget" class="cf-turnstile"
                                            data-sitekey="{{ config('services.turnstile.site_key') }}"
                                            data-callback="onTurnstileSuccess" data-expired-callback="onTurnstileExpired"
                                            data-error-callback="onTurnstileExpired"></div>
                                    </div>
                                    @error('cf-turnstile-response')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="data-diri-section">
                        <h3>Data Diri</h3>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama:</label>
                            <input type="text" class="form-control" name="nama" id="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="angkatan" class="form-label">Angkatan:</label>
                            <input type="text" class="form-control" name="angkatan" id="angkatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                id="email" value="{{ old('email') }}" required maxlength="255">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="strata" class="form-label">Strata:</label>
                            <select class="form-select" name="idstrata" id="strata" required
                                onchange="updatePendidikanOptions()">
                                <option value="">Pilih Strata</option>
                                @foreach ($strata as $value)
                                    <option value="{{ $value->idstrata }}">{{ $value->strata }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pendidikan" class="form-label">Pendidikan:</label>
                            <select class="form-select" name="idpendidikan" id="pendidikan" required>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary w-100" id="to-pertanyaan-button">Berikutnya</button>
                    </div>
                    <div id="pertanyaan-section" class="d-none">
                        <h3>Pertanyaan Survey</h3>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span id="survey-progress-label" class="text-muted">0 dari 0 pertanyaan terjawab</span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div id="survey-progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div id="survey-question-container"></div>
                        <button type="submit" class="btn btn-success w-100 mt-3">Kirim Survey</button>
                    </div>
                </form>
                <div id="terimakasih-section" class="d-none text-center">
                    <h3>Terima Kasih!</h3>
                    <p>Survey Anda telah berhasil dikirim. Terima kasih atas partisipasi Anda!</p>
                    <button class="btn btn-secondary" id="back-to-home-button">Kembali ke Halaman Utama</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>


    <script>
        // Tampilkan notifikasi toast kecil di pojok kanan atas, pengganti alert() bawaan browser
        function showToast(message, type = 'danger') {
            let container = document.getElementById('js-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'js-toast-container';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = 1080;
                document.body.appendChild(container);
            }
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            toast.show();
        }

        const turnstileModalEl = document.getElementById('turnstileModal');
        const turnstileModal = new bootstrap.Modal(turnstileModalEl);
        turnstileModal.show();

        function onTurnstileSuccess() {
            turnstileModal.hide();
        }

        function onTurnstileExpired() {
            turnstileModal.show();
        }

        const dataDiriSection = document.getElementById('data-diri-section');
        const pertanyaanSection = document.getElementById('pertanyaan-section');
        const terimakasihSection = document.getElementById('terimakasih-section');
        const toPertanyaanButton = document.getElementById('to-pertanyaan-button');
        const surveyQuestionContainer = document.getElementById('survey-question-container');
        const backToHomeButton = document.getElementById('back-to-home-button');
        const surveyForm = document.getElementById('data-diri-form');

        const questions = []; // Placeholder for dynamic questions from backend
        const answers = {}; // Stores temporary answers

        // Record the answer when the user selects an option
        function recordAnswer(questionId, answerValue) {
            answers[questionId] = answerValue;
            document.getElementById(`question-card-${questionId}`)?.classList.add('answered');
            updateProgress();
        }

        // Update the "X dari Y pertanyaan terjawab" progress indicator
        function updateProgress() {
            const total = questions.length;
            const done = questions.filter((q) => answers[q.idpertanyaansurvei] !== undefined).length;
            const percent = total ? Math.round((done / total) * 100) : 0;
            document.getElementById('survey-progress-label').textContent = `${done} dari ${total} pertanyaan terjawab`;
            document.getElementById('survey-progress-bar').style.width = `${percent}%`;
        }

        // Show next section (pertanyaan)
        toPertanyaanButton.addEventListener('click', () => {
            const dataDiriForm = document.getElementById('data-diri-form');
            if (dataDiriForm.checkValidity()) {
                dataDiriSection.classList.add('d-none');
                pertanyaanSection.classList.remove('d-none');

                showAllQuestions();
            } else {
                dataDiriForm.reportValidity();
            }
        });

        // Skala jawaban: label, nilai bobot, dan warna tombolnya
        const SKALA_JAWABAN = [
            { label: 'Sangat Tidak Puas', value: 1, color: 'danger' },
            { label: 'Tidak Puas', value: 2, color: 'warning' },
            { label: 'Netral', value: 3, color: 'secondary' },
            { label: 'Puas', value: 4, color: 'primary' },
            { label: 'Sangat Puas', value: 5, color: 'success' },
        ];

        // Display all questions at once
        function showAllQuestions() {
            let questionsHTML = '';
            questions.forEach((question, index) => {
                const qid = question.idpertanyaansurvei;
                const optionsHTML = SKALA_JAWABAN.map((skala) => `
                    <input type="radio" class="btn-check" name="jawaban[${qid}]" id="q${qid}-${skala.value}"
                        value="${skala.label}" autocomplete="off" onchange="recordAnswer(${qid}, ${skala.value})">
                    <label class="btn btn-outline-${skala.color} rounded-pill px-3" for="q${qid}-${skala.value}">${skala.label}</label>
                `).join('');

                questionsHTML += `
                    <div class="survey-question" id="question-card-${qid}">
                        <div class="survey-question-title">
                            <span class="survey-question-number">${index + 1}</span>${question.pertanyaan}
                        </div>
                        <div class="survey-options">
                            ${optionsHTML}
                        </div>
                    </div>
                `;
            });
            surveyQuestionContainer.innerHTML = questionsHTML;
            updateProgress();
        }

        // Function to update pendidikan options based on strata
        function updatePendidikanOptions() {
            var strata = document.getElementById("strata").value;
            var pendidikanSelect = document.getElementById("pendidikan");

            // Kosongkan opsi pendidikan yang ada
            pendidikanSelect.innerHTML = "";

            // Menambahkan opsi default
            var defaultOption = document.createElement("option");
            defaultOption.text = "Pilih Pendidikan";
            defaultOption.value = "";
            pendidikanSelect.appendChild(defaultOption);

            // Mengambil opsi pendidikan berdasarkan strata yang dipilih
            if (strata) {
                $.ajax({
                    url: '{{ url('api/get-pendidikan') }}/' + strata,
                    type: 'GET',
                    success: function(response) {
                        response.forEach(function(pendidikan) {
                            var option = document.createElement("option");
                            option.text = pendidikan.pendidikan;
                            option.value = pendidikan.idpendidikan;
                            pendidikanSelect.appendChild(option);
                        });
                    },
                    error: function() {
                        showToast('Gagal memuat data pendidikan.');
                    }
                });
            }
        }

        // When the pendidikan is selected, show the questions related to the strata and pendidikan
        document.getElementById('pendidikan').addEventListener('change', function() {
            var strataId = document.getElementById('strata').value;
            var pendidikanId = this.value;

            if (strataId && pendidikanId) {
                $.ajax({
                    url: '{{ url('api/get-pertanyaan') }}/' + pendidikanId,
                    type: 'GET',
                    success: function(response) {
                        questions.length = 0; // Clear previous questions
                        questions.push(...response);
                        showAllQuestions();
                    },
                    error: function() {
                        showToast('Gagal memuat pertanyaan survei.');
                    }
                });
            }
        });

        // When submitting, check if all answers are filled out
        surveyForm.addEventListener('submit', (e) => {
            if (!allQuestionsAnswered()) {
                e.preventDefault();
                showToast('Tolong jawab semua pertanyaan sebelum mengirimkan survey!', 'warning');
                return;
            }
        });

        // Check if all questions are answered before submitting
        function allQuestionsAnswered() {
            return questions.every((question) => {
                return answers[question.idpertanyaansurvei] !== undefined;
            });
        }

        // Redirect to the landing page when the "Kembali ke Halaman Utama" button is clicked
        backToHomeButton.addEventListener('click', () => {
            window.location.href = '/'; // Redirect to the landing page (home page)
        });
    </script>
@endsection
