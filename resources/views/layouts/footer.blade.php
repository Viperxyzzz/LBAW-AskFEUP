<footer class="bg-dark p-2 pt-4">
    <div class="text-center">
        <h2 class="text-center mb-0 pb-0">AskFeup</h2>
        <a href="{{ url('/sitemap') }}" class="d-flex justify-content-center mb-3">
            Sitemap
            <i class=" material-symbols-outlined">chevron_right</i>
        </a>
        <nav class="d-flex justify-content-center">
            <a href="{{ url('/') }}" class="text-secondary mx-2">Home</a>
            <a href="{{ url('/browse') }}" class="text-secondary mx-2">Questions</a>
            <a href="{{ url('/users') }}" class="text-secondary mx-2">Users</a>
            <a href="{{ url('/question/create') }}" class="text-secondary mx-2">New question</a>
            <a href="{{ url('/about') }}" class="text-secondary mx-2">About</a>
        </nav>
    </div>
    <hr>
    <p class="mb-0 text-center">AskFeup &copy 2022</p>
</footer>