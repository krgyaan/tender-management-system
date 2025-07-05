<div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 1100;" id="toastWrapper">
    @foreach (['success' => 'success', 'error' => 'danger'] as $type => $class)
        @if (Session::has($type))
            <div class="toast align-items-center text-{{ $class }} bg-light border-0 shadow mb-2 show"
                role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ Session::get($type) }}
                    </div>
                    <button type="button" class="btn btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close">
                        <i class="fa-solid fa-xmark fa-fw"></i>
                    </button>
                </div>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="toast text-danger bg-light border-0 shadow mb-2 show" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close">
                    <i class="fa-solid fa-xmark fa-fw"></i>
                </button>
            </div>
        </div>
    @endif
</div>
