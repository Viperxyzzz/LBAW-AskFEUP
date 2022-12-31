<table class="table mx-4 border">
    <thead class="thead-light">
        <tr>
            <th class="px-3" scope="col">#</th>
            <th scope="col">User</th>
            <th scope="col">Reason</th>
            <th scope="col">Date</th>
            </tr>
    </thead>
    <tbody>
    @foreach ($blocks as $block)
        <tr>
            <th class="px-3" scope="row">{{ $block->block_id }}</th>
            <td> 
                <p class="m-0">
                    <img src="{{asset('storage/'.($block->user()->picture_path).'.jpeg')}}" class="img-fluid rounded-circle" alt="user image" width="25px">
                    <a href="{{url("/users/" . strval($block->user()->user_id))}}"> {{ $block->user()->name }}</a>
                </p>
            </td>
            <td>{{ $block->reason }}</td>
            <td>{{ $block->date }}</td>
        </tr>
    @endforeach
    </tbody>
</table>