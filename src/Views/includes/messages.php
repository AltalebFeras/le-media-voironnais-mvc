<?php
// these error in singular for one message in case of we have only one message of success or error 
// should see the modification made on function add list in ListsController to adapte this type of error returing in order to be applied on all ctrls
if (isset($_SESSION['error'])): ?>
    <!-- Alert Messages -->
    <div class="custom-alert custom-alert-danger">
        <p>
            <?= $_SESSION['error']; ?>
        </p>
        <button class="custom-btn-close" onclick="this.parentElement.remove()" aria-label="Close">❌</button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php
// display all errors when making ctrl on submit button so all the cases of if withe the results errors should be stocked at the last verification of the form data and it display by throw and exception to allow the code to jump to catch case so we can get the errors .
if (!empty($_SESSION['errors'])): ?>
    <ul class="error-list">
        <?php foreach ($_SESSION['errors'] as $key => $error): ?>
            <li class="error-item">
                <?= $error ?>
                <button class="custom-btn-close" onclick="
                    const li = this.parentElement;
                    const ul = li.parentElement;
                    li.remove();
                    if (ul.children.length === 0) {
                        ul.remove();
                    }
                " aria-label="Close">❌</button>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php unset($_SESSION['errors']); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto fade-out alerts and error lists after 5 seconds
            const alerts = document.querySelectorAll('.custom-alert, .error-list');

            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.classList.add('fade-out');

                    // Remove element after animation completes (assuming 1s animation duration)
                    setTimeout(function() {
                        if (alert.parentElement) {
                            alert.remove();
                        }
                    }, 1000);
                }, 10000);
            });
        });
    </script>
<?php endif; ?>

<?php
// Toast notifications for success and info messages
if (isset($_SESSION['success']) || isset($_SESSION['info']) || isset($_SESSION['warning'])):
    $toastMsg = isset($_SESSION['success']) ? $_SESSION['success'] : (isset($_SESSION['info']) ? $_SESSION['info'] : $_SESSION['warning']);
    $toastType = isset($_SESSION['success']) ? 'success' : (isset($_SESSION['info']) ? 'info' : 'warning');
    unset($_SESSION['success'], $_SESSION['info'], $_SESSION['warning']);
?>
    <div class="toast toast-<?= $toastType ?>" id="toast">
        <?= htmlspecialchars($toastMsg) ?>
        <button class="toast-close-btn" aria-label="Close">❌</button>
    </div>
    <style>
        .toast {
            position: fixed;
            top: 110px;
            right: 30px;
            background: #1f1f1f;
            color: #eee;
            padding: 14px 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 255, 213, 0.3);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.4s ease;
            z-index: 9999;
            min-width: 220px;
            font-size: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toast-close-btn {
            background: none;
            border: none;
            color: #eee;
            font-size: 0.9rem;
            cursor: pointer;
            margin-left: 10px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toast-close-btn:hover {
            color: #ff6b6b;
        }

        .toast-success {
            border-left: 6px solid #00ffd5;
        }

        .toast-info {
            border-left: 6px solid #2196f3;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .toast.hide {
            opacity: 0;
            transform: translateX(-100%);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toast = document.getElementById('toast');
            if (toast) {
                setTimeout(function() {
                    toast.classList.add('show');
                }, 100);

                // Handle close button click
                const closeBtn = toast.querySelector('.toast-close-btn');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        dismissToast();
                    });
                }

                // Auto-dismiss timer
                var toastTimer = setTimeout(function() {
                    dismissToast();
                }, 7000); // auto-dismiss after 7 seconds
                
                // Function to dismiss toast
                function dismissToast() {
                    toast.classList.remove('show');
                    toast.classList.add('hide');

                    // Wait for animation to complete before removing
                    setTimeout(function() {
                        if (toast.parentElement) toast.remove();
                    }, 400);
                }
            }
        });
    </script>
<?php endif; ?>