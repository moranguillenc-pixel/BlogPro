<div class="bg-white rounded-lg shadow p-4 mb-4">
    <!-- Autor -->
    <div class="flex items-center mb-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full mr-3"></div>
        <div>
            <p class="font-semibold text-gray-800">{{ $post->user->name }}</p>
            <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
        </div>
    </div>

    <!-- Contenido -->
    <p class="text-gray-800 mb-3 whitespace-pre-wrap">{{ $post->content }}</p>

    <!-- Acciones -->
    <div class="flex justify-between text-sm border-t pt-3 text-gray-600">
        <form action="{{ route('posts.like', $post) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="flex items-center space-x-1 hover:text-blue-600 font-medium">
                <span>{{ $post->likedBy(auth()->user()) ? 'Liked' : 'Like' }}</span>
                <span>• {{ $post->likes->count() }}</span>
            </button>
        </form>
        <button onclick="toggleComments('{{ $post->id }}')" 
                class="hover:text-blue-600 font-medium">
            {{ $post->comments->count() }} Comentario{{ $post->comments->count() !== 1 ? 's' : '' }}
        </button>
    </div>

    <!-- Formulario de comentario -->
    <div id="comment-form-{{ $post->id }}" class="mt-4 hidden">
        <form action="{{ route('comments.store', $post) }}" method="POST" class="flex space-x-2">
            @csrf
            <input type="text" name="content" 
                   class="flex-1 p-2 border rounded-lg text-sm" 
                   placeholder="Escribe un comentario..." required>
            <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded text-sm">
                Enviar
            </button>
        </form>
    </div>

    <!-- Lista de comentarios -->
    <div id="comments-{{ $post->id }}" class="mt-3 space-y-2">
        @forelse($post->comments as $comment)
            @include('comments._comment', ['comment' => $comment, 'post' => $post])
        @empty
            <!-- Sin comentarios -->
        @endforelse
    </div>
</div>

<script>
function toggleComments(id) {
    const form = document.getElementById('comment-form-' + id);
    if (form) form.classList.toggle('hidden');
}
</script>