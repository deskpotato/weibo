<div class="list-group-item">
    <img src="{{ $user->gravatar() }}" alt="{{ $user->name }}" class="mr-3" width="32">
    <a href="{{ route('users.show',$user->id) }}">
        {{ $user->name }}
    </a>
    @can('destroy',$user)
        <form action="{{ route('users.destroy',$user->id) }}" class="float-right" method="POST">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button class="btn btn-sm btn-danger delete-btn">删除</button>
        </form>
    @endcan
</div>