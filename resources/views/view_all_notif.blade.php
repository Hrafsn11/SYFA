@extends('layouts.app')

@section('page-style')
<style>
    
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="card mt-1">
            <div class="card-header border-bottom">
                <div class="row col-12">
                    <div class="col-6 mt-2">
                        <h4 class="mb-0">Semua Notifikasi</h4>
                    </div>
                    <div class="col-6 mt-2 text-end">
                        <form action="{{ url('notif-read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="read-all text-body cursor-pointer" data-bs-toggle="tooltip" title="Tandai semua telah dibaca" style="background: none; border: none; padding: 0;">
                                <i class='tf-icons ti ti-square-check' style="width: 20px; height: 20px;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <section id="accordion-with-margin">
                    <div class="accordion" id="notifAccordion">
                        <!-- TERBARU -->
                        <div class="card border-0 mb-2 mt-2">
                            <div class="card-header" id="headingRecent" data-bs-toggle="collapse" data-bs-target="#collapseRecent" aria-expanded="true" aria-controls="collapseRecent">
                                <span class="collapse-title">TERBARU ({{ count($recent) }})</span>
                            </div>
                            <div id="collapseRecent" class="collapse show" aria-labelledby="headingRecent" data-bs-parent="#notifAccordion">
                                <ul class="list-group list-group-flush">
                                    @foreach ($recent as $notif)
                                        <li class="list-group-item list-group-item-action {{ $notif->status == 'read' ? 'marked-as-read' : '' }}">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <a href="{{ url('notif-read/' . $notif->id) }}" class="text-black">
                                                        <p class="mb-0 notif-text" id="text-{{ $notif->id }}">{{ strip_tags($notif->content) }}</p>
                                                        <small class="text-muted create">{{ $notif->created_at }}</small>
                                                    </a>
                                                    <span class="see-more" id="see-more-{{ $notif->id }}"></span>
                                                </div>
                                                @if ($notif->status == 'unread')
                                                    <div class="flex-shrink-0">
                                                        <span class="badge-dot"></span>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- LEBIH LAMA -->
                        <div class="card border-0">
                            <div class="card-header" id="headingOld" data-bs-toggle="collapse" data-bs-target="#collapseOld" aria-expanded="true" aria-controls="collapseOld">
                                <span class="collapse-title">LEBIH LAMA ({{ count($previous) }})</span>
                            </div>
                            <div id="collapseOld" class="collapse show" aria-labelledby="headingOld" data-bs-parent="#notifAccordion">
                                <ul class="list-group list-group-flush">
                                    @foreach ($previous as $notif)
                                        <li class="list-group-item list-group-item-action {{ $notif->status == 'read' ? 'marked-as-read' : '' }}">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <a href="{{ url('notif-read/' . $notif->id) }}" class="text-black">
                                                        <p class="mb-0 notif-text" id="text-{{ $notif->id }}">{{ strip_tags($notif->content) }}</p>
                                                        <small class="text-muted create">{{ $notif->created_at }}</small>
                                                    </a>
                                                    <span class="see-more" id="see-more-{{ $notif->id }}"></span>
                                                </div>
                                                @if ($notif->status == 'unread')
                                                    <div class="flex-shrink-0">
                                                        <span class="badge-dot"></span>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        const allTexts = document.querySelectorAll('[id^="text-"]');

        allTexts.forEach(text => {
            const notifId = text.id.replace('text-', '');
            const fullText = text.textContent.trim();
            const seeMore = document.getElementById(`see-more-${notifId}`);

            function truncateText(str, maxLength) {
                return str.length > maxLength ? str.substring(0, maxLength) + "..." : str;
            }

            function toggleText() {
                if (text.classList.contains('collapsed')) {
                    text.textContent = truncateText(fullText, 60);
                    seeMore.textContent = 'See More...';
                } else {
                    text.textContent = fullText;
                    seeMore.textContent = 'See Less';
                }
                text.classList.toggle('collapsed');
            }

            if (fullText.length > 60) {
                text.textContent = truncateText(fullText, 60);
                seeMore.textContent = 'See More...';
                seeMore.style.display = 'inline';
                seeMore.addEventListener('click', toggleText);
                text.classList.add('collapsed');
            }
        });
    });
</script>
@endsection
