<ul>
    <li>
        <div class="mitra-node @if($node['code_mitra'] === null) parent-node @endif @if($loop->first ?? false) current-user @endif">
            @if(!empty($node['picture_profile']))
                <img src="{{ asset('storage/' . $node['picture_profile']) }}" alt="{{ $node['name'] }}" class="mitra-image">
            @else
                <div class="mitra-image" style="background-color: #007bff; display: flex; align-items: center; justify-content: center; color: white;">
                    {{ strtoupper(substr($node['name'], 0, 1)) }}
                </div>
            @endif
        </div>
        @if(!empty($node['children']))
            @foreach($node['children'] as $child)
                @include('pages.mitra.tree_node', ['node' => $child])
            @endforeach
        @endif
    </li>
</ul>