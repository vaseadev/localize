<template>
    <div style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">
        <div class="error-page">
            <h2 class="headline text-warning">404</h2>
            <div class="error-content">
                <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>
                <p>
                    We could not find the page you were looking for.
                    Meanwhile, you may
                    <router-link :to="{ name: 'Home' }"><a href="#">return to dashboard</a></router-link>.
                </p>
            </div>
        </div>
    </div>
</template>
