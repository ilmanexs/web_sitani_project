@if(session()->has('failures'))
    <div id="error-container" class="mb-4">
        <x-ui.card>
            <div class="w-full max-h-64">
                <div class=" w-full h-full flex justify-between mb-2.5 flex-wrap gap-y-2.5">
                    <x-ui.title :title="'Daftar Kesalahan Import Data'"/>
                    <button id="btn-bersihkan" type="button" class="button-bersihkan-error btn btn-soft btn-error">
                        Bersihkan
                    </button>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="table overflow-x-auto">
                        <thead>
                        <tr>
                            <th>Baris</th>
                            <th>Kolom</th>
                            <th>Pesan Kesalahan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (session('failures') as $failure)
                            <tr>
                                <td>{{ $failure['row'] }}</td>
                                <td>{{ $failure['attribute'] }}</td>
                                <td>{{ implode(', ', $failure['errors']) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-ui.card>
    </div>
@endif

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnHapus = document.getElementById('btn-bersihkan');
            if (btnHapus) {
                document.getElementById('btn-bersihkan').addEventListener('click', function () {
                    const errorContainer = document.getElementById('error-container');
                    if (errorContainer) {
                        errorContainer.remove();
                    }
                });
            }
        });
    </script>
@endonce
