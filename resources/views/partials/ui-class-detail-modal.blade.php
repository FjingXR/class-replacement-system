@php
    $modalId = $modalId ?? 'classModal';
    $overlayOnclick = $overlayOnclick ?? 'closeModalOutside(event)';
@endphp

<div class="modal-overlay" id="{{ $modalId }}" style="display:none" onclick="{{ $overlayOnclick }}">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modalTitle">Class Details</span>
            <span class="modal-status-badge" id="modalStatusBadge">Normal</span>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            @hasSection('modal-footer')
                @yield('modal-footer')
            @else
                <button class="btn-close-modal" onclick="closeModal()">Close</button>
            @endif
        </div>
    </div>
</div>
