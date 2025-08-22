@extends('layouts.app')
@section('page-title', 'Edit Your Profile')
@section('content')
    <section>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Hi👋 {{ $user->name }}</h5>
                    @include('partials.messages')
                    <div class="text-center">
                        <img src="{{ asset('uploads/' . $user->image) }}" alt="{{ $user->name }}" class="img-fluid"
                            style="width: 100px; height: 100px; border-radius: 50%;">
                    </div>
                    <div class="new-user-info">
                        <form method="POST" action="{{ route('profile.update', $user->id) }}"
                            enctype="multipart/form-data" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="name">Name:</label>
                                    <input type="text" name="name" class="form-control" id="name"
                                        value="{{ $user->name }}">
                                    <small>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="mobile">Mobile:</label>
                                    <input type="number" name="mobile" class="form-control" id="mobile"
                                        value="{{ $user->mobile }}">
                                    <small>
                                        <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="email">Email:</label>
                                    <input type="email" name="email" class="form-control" id="email"
                                        value="{{ $user->email }}" @disabled(true)>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="role">Role (designation):</label>
                                    <input type="text" name="role" class="form-control" id="role"
                                        value="{{ $user->role }} ({{ $user->designation }})"
                                        @disabled(true)>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="image">Image:</label>
                                    <input type="file" name="image" id="image" class="form-control"
                                        accept=".png,.jpeg,.jpg">
                                    <small>
                                        <span class="text-danger">{{ $errors->first('img') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="address">Address:</label>
                                    <textarea name="address" class="form-control" id="address">{{ $user->address }}</textarea>
                                    <small>
                                        <span class="text-danger">{{ $errors->first('address') }}</span>
                                    </small>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#forgotPasswordModal">
                                        Change Password
                                    </button>
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- forgot password modal  --}}
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('profile.change_password', $user->id) }}"
                        class="needs-validation" novalidate>
                        @csrf
                        <div class="form-group">
                            <label for="email">Old Password:</label>
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <input type="password" name="old_password" class="form-control" id="old_password" required>
                            <small>
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" name="new_password" class="form-control" id="new_password" required>
                            <small>
                                <span class="text-danger">{{ $errors->first('new_password') }}</span>
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" name="confirm_password" class="form-control" id="confirm_password"
                                required>
                            <small>
                                <span class="text-danger">{{ $errors->first('confirm_password') }}</span>
                            </small>
                        </div>
                        <div class="text-end">
                            <button type="submit" name="submit" class="btn btn-primary">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            })
        });

        FilePond.registerPlugin(FilePondPluginImagePreview);
        $(document).ready(function() {
            $('#image').filepond({
                storeAsFile: true,
                credits: false,
                alloeImagePreview: true,
            });
        });
    </script>
@endpush
