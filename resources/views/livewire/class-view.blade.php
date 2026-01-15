<div>
    <div class="content-header">
        <h1 class="content-title">View Class</h1>
    </div>

    <div class="student-table">

        <h2 class="mb-2">{{ $class->class_name }}</h2>

        @if ($class->video)
            <div class="mb-3">
                <video width="100%" height="500" controls>
                    <source src="{{ asset('storage/' . $class->video) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            @if ($class->youtube_url)
                <p>More Reference: <a href="{{ $class->youtube_url }}" target="_blank">{{ $class->youtube_url }}</a></p>
            @endif
            @if ($class->notes)
                <p>
                    <a href="{{ asset('storage/' . $class->notes) }}" download class="btn btn-primary">
                        Download Notes
                    </a>
                </p>
            @endif
        @elseif ($class->youtube_url)
            @php
                function getEmbedUrl($url) {
                    if (strpos($url, 'youtu.be') !== false) {
                        // For youtu.be links
                        preg_match('/youtu\.be\/([^\?]+)/', $url, $matches);
                    } else {
                        // For youtube.com/watch?v=... links
                        preg_match('/v=([^&]+)/', $url, $matches);
                    }

                    return isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : '';
                }
            @endphp
            <div class="mb-3">
                <iframe width="100%" height="520" src="{{ getEmbedUrl($class->youtube_url) }}" frameborder="0" allowfullscreen></iframe>
            </div>
            @if ($class->notes)
                <p>
                    <a href="{{ asset('storage/' . $class->notes) }}" download class="btn btn-primary">
                        Download Notes
                    </a>
                </p>
            @endif
        @elseif ($class->notes)
            <p>
                <a href="{{ asset('storage/' . $class->notes) }}" download class="btn btn-primary">
                    Download Notes
                </a>
            </p>
        @else
            <p class="text-danger">No content available for this class.</p>
        @endif
    </div>
</div>
