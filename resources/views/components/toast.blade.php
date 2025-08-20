<!-- Handle SweetAlert2 notifications -->
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('swal', (data) => {
            Swal.fire({
                title: data[0].title,
                text: data[0].text,
                icon: data[0].icon,
                position: 'center',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                },
                customClass: {
                    container: 'custom-swal-container',
                    popup: 'custom-swal-popup',
                    title: 'custom-swal-title',
                    icon: 'custom-swal-icon'
                }
            });
        });
    });
</script>

<style>
    .custom-swal-container {
        display: flex;
        justify-content: center;
        align-items: flex-start !important;
        padding-top: 2vh !important;
        /* Adjust this value to move higher or lower */
    }

    .custom-swal-popup {
        width: 400px !important;
        font-size: 1em !important;
        margin-top: 0 !important;
    }

    .custom-swal-title {
        font-size: 1.5em !important;
    }

    .custom-swal-icon {
        font-size: 1em !important;
    }
</style>
