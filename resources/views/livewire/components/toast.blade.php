<div>
    <script>
        Livewire.on('toast', data => {
            const toast = data[0];
            toastr.options = {
                "positionClass": "toast-bottom-right" // ou "toast-bottom-left", "toast-bottom-center"
            };

            toastr[toast.type](toast.message);
        });
    </script>
</div>