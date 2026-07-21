<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-triangle-exclamation text-danger fa-2x"></i>
                </div>
                <h5 class="mb-2">Hapus data ini?</h5>
                <p class="text-muted mb-4">Tindakan ini tidak bisa dibatalkan.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmDeleteButton">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmModalEl = document.getElementById('confirmDeleteModal');
        if (!confirmModalEl) return;

        const confirmModal = new bootstrap.Modal(confirmModalEl);
        let formToSubmit = null;

        document.querySelectorAll('.btn-delete-confirm').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                formToSubmit = btn.closest('form');
                confirmModal.show();
            });
        });

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    });
</script>
