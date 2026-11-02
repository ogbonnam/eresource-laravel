{{--
    Shared multi-file uploader.
    Save as: resources/views/teacher/lesson-plans/partials/file-uploader.blade.php

    Create page:  @include('teacher.lesson-plans.partials.file-uploader')
    Edit page:    @include('teacher.lesson-plans.partials.file-uploader', ['lessonPlan' => $lessonPlan])
--}}

@php($existingFiles = isset($lessonPlan) ? collect($lessonPlan->all_files) : collect())

@if($existingFiles->isNotEmpty())
    <div class="mb-6">
        <p class="text-sm font-medium text-gray-700">Files already uploaded</p>

        <ul class="mt-2 space-y-2">
            @foreach($existingFiles as $existing)
                <li class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm">
                    <div class="min-w-0">
                        <a href="{{ $existing['url'] }}" target="_blank"
                           class="block truncate font-medium text-indigo-600 hover:text-indigo-700">
                            {{ $existing['name'] }}
                        </a>
                        @if(!empty($existing['size']))
                            <p class="text-xs text-gray-500">{{ number_format($existing['size'] / 1024 / 1024, 2) }} MB</p>
                        @endif
                    </div>

                    <label class="ml-4 flex shrink-0 items-center gap-2 text-xs font-semibold text-red-600">
                        <input type="checkbox" name="remove_files[]" value="{{ $existing['path'] }}"
                               class="existing-file-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500">
                        Remove
                    </label>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<label
    id="dropzone"
    for="files"
    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition hover:border-indigo-400 hover:bg-indigo-50"
>
    <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
    </svg>

    <p class="mt-3 text-sm font-medium text-gray-700">
        <span class="text-indigo-600">Click to choose files</span> or drag and drop them here
    </p>

    <p class="mt-1 text-xs text-gray-500">
        PDF, DOC, DOCX, PPT, PPTX or ZIP. Up to 20 files, 20 MB each.
    </p>
</label>

<input
    id="files"
    type="file"
    name="files[]"
    multiple
    accept=".pdf,.doc,.docx,.ppt,.pptx,.zip"
    class="sr-only"
>

<ul id="file-list" class="mt-4 space-y-2"></ul>

<p id="file-summary" class="mt-3 hidden text-sm text-gray-600"></p>
<p id="file-warning" class="mt-2 hidden text-sm font-medium text-red-600"></p>

<script>
(function () {
    const MAX_FILES = 20;
    const MAX_BYTES = 20 * 1024 * 1024;
    const ALLOWED = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip'];

    const input = document.getElementById('files');
    const list = document.getElementById('file-list');
    const summary = document.getElementById('file-summary');
    const warning = document.getElementById('file-warning');
    const dropzone = document.getElementById('dropzone');
    const submitBtn = document.getElementById('submit-btn'); // optional
    const existingBoxes = Array.from(document.querySelectorAll('.existing-file-checkbox'));

    let selected = new DataTransfer();

    function remainingExisting() {
        return existingBoxes.filter(function (b) { return !b.checked; }).length;
    }

    function ext(name) {
        const parts = name.toLowerCase().split('.');
        return parts.length > 1 ? parts.pop() : '';
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function addFiles(fileList) {
        Array.from(fileList).forEach(function (f) {
            const duplicate = Array.from(selected.files).some(function (x) {
                return x.name === f.name && x.size === f.size;
            });
            if (!duplicate) selected.items.add(f);
        });
        input.files = selected.files;
        render();
    }

    function removeAt(index) {
        const next = new DataTransfer();
        Array.from(selected.files).forEach(function (f, i) {
            if (i !== index) next.items.add(f);
        });
        selected = next;
        input.files = selected.files;
        render();
    }

    function render() {
        list.innerHTML = '';
        const files = Array.from(selected.files);
        let problem = '';
        let total = 0;

        files.forEach(function (f, i) {
            total += f.size;

            const bad = !ALLOWED.includes(ext(f.name)) || f.size > MAX_BYTES;
            if (!ALLOWED.includes(ext(f.name))) problem = '"' + f.name + '" is not an allowed file type.';
            else if (f.size > MAX_BYTES) problem = '"' + f.name + '" is larger than 20 MB.';

            const li = document.createElement('li');
            li.className = 'flex items-center justify-between rounded-lg border px-4 py-3 text-sm '
                + (bad ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white');

            const left = document.createElement('div');
            left.className = 'min-w-0';

            const name = document.createElement('p');
            name.className = 'truncate font-medium text-gray-800';
            name.textContent = f.name;

            const meta = document.createElement('p');
            meta.className = 'text-xs text-gray-500';
            meta.textContent = ext(f.name).toUpperCase() + ' · ' + formatSize(f.size);

            left.appendChild(name);
            left.appendChild(meta);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'ml-4 shrink-0 rounded-md px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-100';
            remove.textContent = 'Remove';
            remove.addEventListener('click', function () { removeAt(i); });

            li.appendChild(left);
            li.appendChild(remove);
            list.appendChild(li);
        });

        if (files.length + remainingExisting() > MAX_FILES) {
            problem = 'A lesson plan can have at most ' + MAX_FILES + ' files. Put the rest in a ZIP file.';
        }

        if (files.length) {
            summary.classList.remove('hidden');
            summary.textContent = files.length + (files.length === 1 ? ' new file' : ' new files') + ' selected · ' + formatSize(total);
        } else {
            summary.classList.add('hidden');
        }

        if (problem) {
            warning.classList.remove('hidden');
            warning.textContent = problem;
        } else {
            warning.classList.add('hidden');
        }

        if (submitBtn) submitBtn.disabled = !!problem;
    }

    input.addEventListener('change', function () { addFiles(input.files); });
    existingBoxes.forEach(function (b) { b.addEventListener('change', render); });

    ['dragenter', 'dragover'].forEach(function (evt) {
        dropzone.addEventListener(evt, function (e) {
            e.preventDefault();
            dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
        });
    });

    ['dragleave', 'drop'].forEach(function (evt) {
        dropzone.addEventListener(evt, function (e) {
            e.preventDefault();
            dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
        });
    });

    dropzone.addEventListener('drop', function (e) { addFiles(e.dataTransfer.files); });
})();
</script>