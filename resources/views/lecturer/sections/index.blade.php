@extends('lecturer.layouts.app')

@section('content')
    <div class="sections-page">
        <section class="sections-hero">
            <div>
                <p class="sections-kicker">Sections</p>
                <h1 class="sections-title">Atur urutan section</h1>
            </div>

            <a href="{{ route('lecturer.sections.create') }}" class="sections-primary-btn">
                Tambah Section Baru
            </a>
        </section>

        @if (session('success'))
            <div class="sections-alert success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="sections-alert error">{{ session('error') }}</div>
        @endif

        <section class="sections-card bg-white">
            <div class="sections-card-head">
                <div>
                    <p class="sections-card-kicker">Daftar</p>
                    <h2 class="sections-card-title">Susunan section aktif</h2>
                </div>
                <p class="sections-card-note">Tarik untuk urutkan.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="sections-table" id="sortable-sections">
                    <thead>
                        <tr>
                            <th>Urutan</th>
                            <th>Nama Section</th>
                            <th>Jumlah Challenge</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sections as $section)
                            <tr class="sortable-row" data-id="{{ $section->id }}">
                                <td class="sections-order order-number">
                                    <span class="sections-order-icon">&#9776;</span>
                                    <span>{{ $section->order }}</span>
                                </td>
                                <td>
                                    <div class="sections-name-wrap">
                                        <strong>{{ $section->name }}</strong>
                                        <span>ID Section #{{ $section->id }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="sections-badge">{{ $section->challenges_count }} challenge</span>
                                </td>
                                <td>
                                    <div class="sections-actions">
                                        <a href="{{ route('lecturer.sections.edit', $section->id) }}" class="sections-btn warn">Edit</a>
                                        <a href="{{ route('lecturer.challenges.index', ['section_id' => $section->id]) }}" class="sections-btn accent">Lihat Challenge</a>
                                        <form action="{{ route('lecturer.sections.destroy', $section->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus section ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sections-btn danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="sections-empty">
                                        Belum ada section. Tambahkan section pertama untuk mulai menyusun alur belajar.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="sections-pagination">
                {{ $sections->links('pagination::tailwind') }}
            </div>
        </section>
    </div>

    <style>
        .sections-page {
            max-width: 1200px;
            margin: 0 auto;
        }

        .sections-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 24px;
            padding: 28px;
            border-radius: 30px;
            border: 1px solid rgba(255, 228, 236, 0.14);
            background: rgba(74, 19, 39, 0.78);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.22);
        }

        .sections-kicker {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            color: rgba(255, 228, 236, 0.75);
        }

        .sections-title {
            margin: 12px 0 0;
            font-size: 42px;
            line-height: 1.15;
            color: #fff;
            font-weight: 700;
        }

        .sections-copy {
            margin: 14px 0 0;
            max-width: 760px;
            color: rgba(255, 240, 244, 0.76);
            line-height: 1.8;
        }

        .sections-primary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 20px;
            border-radius: 18px;
            background: linear-gradient(90deg, #c0265f, #ec4899);
            color: #fff;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 16px 30px rgba(190, 24, 93, 0.25);
        }

        .sections-alert {
            margin-bottom: 16px;
            padding: 14px 18px;
            border-radius: 18px;
            font-weight: 600;
        }

        .sections-alert.success {
            background: rgba(220, 252, 231, 0.96);
            color: #166534;
        }

        .sections-alert.error {
            background: rgba(254, 226, 226, 0.96);
            color: #991b1b;
        }

        .sections-card {
            padding: 24px;
            border-radius: 30px;
        }

        .sections-card-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .sections-card-kicker {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: #be185d;
        }

        .sections-card-title {
            margin: 8px 0 0;
            color: #1f2937;
            font-size: 30px;
            font-weight: 700;
        }

        .sections-card-note {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        .sections-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sections-table thead th {
            padding: 14px 16px;
            text-align: left;
            background: linear-gradient(90deg, #9f1d4f, #d9467a);
            color: #fff;
            font-size: 12px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .sections-table thead th:first-child {
            border-top-left-radius: 18px;
        }

        .sections-table thead th:last-child {
            border-top-right-radius: 18px;
        }

        .sections-table tbody td {
            padding: 18px 16px;
            border-bottom: 1px solid #f3e8ef;
            color: #334155;
            vertical-align: middle;
        }

        .sections-table tbody tr:hover {
            background: #fff7fa;
        }

        .sections-order {
            font-weight: 700;
            color: #4a1327;
            cursor: move;
            white-space: nowrap;
        }

        .sections-order-icon {
            display: inline-block;
            margin-right: 8px;
            color: #be185d;
        }

        .sections-name-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sections-name-wrap strong {
            color: #1f2937;
            font-size: 16px;
        }

        .sections-name-wrap span {
            color: #94a3b8;
            font-size: 13px;
        }

        .sections-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #fff1f6;
            color: #be185d;
            font-weight: 700;
            font-size: 13px;
        }

        .sections-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        .sections-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 14px;
            border: 0;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .sections-btn.warn {
            background: #f59e0b;
        }

        .sections-btn.accent {
            background: #a21caf;
        }

        .sections-btn.danger {
            background: #ef4444;
        }

        .sections-empty {
            padding: 24px;
            text-align: center;
            color: #64748b;
        }

        .sections-pagination {
            margin-top: 18px;
        }

        @media (max-width: 768px) {
            .sections-hero,
            .sections-card-head {
                flex-direction: column;
                align-items: stretch;
            }

            .sections-title {
                font-size: 32px;
            }

            .sections-card {
                padding: 18px;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#sortable-sections tbody").sortable({
                update: function() {
                    const orderedIds = $(".sortable-row").map(function() {
                        return $(this).data("id");
                    }).get();

                    $.ajax({
                        url: "{{ route('lecturer.sections.reorder') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            orderedIds: orderedIds
                        },
                        success: function() {
                            $(".sortable-row").each(function(index) {
                                $(this).find(".order-number span:last").text(index + 1);
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            }).disableSelection();
        });
    </script>
@endsection
