<?php
// these error in singular for one message in case of we have only one message of success or error 
// should see the modification made on function add list in ListsController to adapte this type of error returing in order to be applied on all ctrls
if (isset($_SESSION['error'])): ?>
    <!-- Alert Messages -->
    <div class="custom-alert custom-alert-danger fade-out">
        <p>
            <?= $_SESSION['error']; ?>
        </p>
        <button class="custom-btn-close" onclick="this.parentElement.remove()" aria-label="Close">❌</button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="custom-alert custom-alert-success fade-out">
        <p>
            <?= $_SESSION['success']; ?>
        </p>
        <button class="custom-btn-close" onclick="this.parentElement.remove()" aria-label="Close">❌</button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['info'])): ?>
    <div class="custom-alert custom-alert-info -out">
        <p>
            <?= $_SESSION['info']; ?>
        </p>
        <button class="custom-btn-close" onclick="this.parentElement.remove()" aria-label="Close">❌</button>
    </div>
    <?php unset($_SESSION['info']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['warning'])): ?>
    <div class="custom-alert custom-alert-warning fade-out">
        <p>
            <?= $_SESSION['warning']; ?>
        </p>
        <button class="custom-btn-close" onclick="this.parentElement.remove()" aria-label="Close">❌</button>
    </div>
    <?php unset($_SESSION['warning']); ?>
<?php endif; ?>
<?php
// display all errors when making ctrl on submit button so all the cases of if withe the results errors should be stocked at the last verification of the form data and it display by throw and exception to allow the code to jump to catch case so we can get the errors .
if (!empty($_SESSION['errors'])): ?>
    <ul class="error-list fade-out">
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
<?php endif; ?>
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