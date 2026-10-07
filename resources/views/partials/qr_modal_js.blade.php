@include('partials.qr_modal')
    <!-- Add / Edit Pre-Registration Modal -->
    <div id="inviteModal" class="modal-root">
        <div class="modal-backdrop" onclick="closeInviteModal()"></div>
        <div class="modal-panel">
            <div class="modal-head">
                <h3 id="modalTitle">New Pre-registration</h3>
                <button type="button" class="btn btn-ghost btn-sm" onclick="closeInviteModal()">✕</button>
            </div>
            <form id="inviteForm" action="{{ route('pre_registrations.store') }}" method="POST">
                @csrf
                <input type="hidden" id="invite_method" name="_method" value="POST">

                <div class="modal-body">
                    <!-- Section 1: Visitor Information -->
                    <div class="section-label">Visitor Information</div>
                    <div class="field" style="margin-bottom: 12px;">
                        <label for="inv_name">Full Name *</label>
                        <input type="text" id="inv_name" name="name" required placeholder="Full name">
                    </div>
                    <div class="field" style="margin-bottom: 12px;">
                        <label for="inv_company_name">Company / Organization</label>
                        <input type="text" id="inv_company_name" name="company_name" placeholder="Company name">
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="inv_email">Email (optional) *</label>
                            <input type="email" id="inv_email" name="email" required placeholder="visitor@company.com">
                        </div>
                        <div class="field">
                            <label for="inv_phone">Phone number</label>
                            <div class="phone-group" style="display: flex; gap: 8px;">
                                <select name="phone_code" id="inv_phone_code" style="width: 105px; flex: none;">
                                    <option value="971" selected>+971</option>
                                    <option value="966">+966</option>
                                    <option value="974">+974</option>
                                    <option value="965">+965</option>
                                    <option value="973">+973</option>
                                    <option value="968">+968</option>
                                    <option value="1">+1</option>
                                    <option value="44">+44</option>
                                    <option value="91">+91</option>
                                </select>
                                <input type="tel" id="inv_phone" name="phone" placeholder="50 000 0000" style="flex: 1;">
                            </div>
                        </div>
                        <div class="field">
                            <label for="inv_id_type_id">ID type</label>
                            <select id="inv_id_type_id" name="id_type_id">
                                @foreach ($idTypes as $idType)
                                    <option value="{{ $idType->id }}" {{ str_contains(strtolower($idType->name), 'emirates') ? 'selected' : '' }}>{{ $idType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_id_number">ID Document Number</label>
                            <input type="text" id="inv_id_number" name="id_number" placeholder="Emirates ID / Passport number">
                        </div>
                    </div>

                    <!-- Section 2: Visit Schedule -->
                    <div class="section-label">Visit Schedule</div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="inv_visit_date">Visit Date *</label>
                            <input type="date" id="inv_visit_date" name="visit_date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="field">
                            <label for="inv_expected_arrival">Expected Arrival *</label>
                            <select id="inv_expected_arrival" name="expected_arrival" required>
                                @for ($h = 7; $h <= 21; $h++)
                                    @foreach (['00', '15', '30', '45'] as $m)
                                        @php
                                            $h12 = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                                            $ampm = $h >= 12 ? 'PM' : 'AM';
                                            $displayTime = sprintf('%02d:%s %s', $h12, $m, $ampm);
                                            $valTime = sprintf('%02d:%s', $h, $m);
                                        @endphp
                                        <option value="{{ $valTime }}" {{ $valTime === '10:00' ? 'selected' : '' }}>{{ $displayTime }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_expected_departure">Expected Departure</label>
                            <select id="inv_expected_departure" name="expected_departure">
                                <option value="">Not set</option>
                                @for ($h = 7; $h <= 22; $h++)
                                    @foreach (['00', '15', '30', '45'] as $m)
                                        @php
                                            $h12 = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                                            $ampm = $h >= 12 ? 'PM' : 'AM';
                                            $displayTime = sprintf('%02d:%s %s', $h12, $m, $ampm);
                                            $valTime = sprintf('%02d:%s', $h, $m);
                                        @endphp
                                        <option value="{{ $valTime }}">{{ $displayTime }}</option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_duration">Duration</label>
                            <select id="inv_duration" name="duration">
                                <option value="30">30 min</option>
                                <option value="60" selected>1 hour</option>
                                <option value="120">2 hours</option>
                                <option value="240">Half day</option>
                                <option value="480">Full day</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 3: Recurring Visit -->
                    <div class="section-label">Recurring Visit</div>
                    <div class="field">
                        <label for="inv_recurring">Recurring Visit</label>
                        <select id="inv_recurring" name="recurring">
                            <option value="none">One-time visit</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <!-- Section 4: Visit Details -->
                    <div class="section-label">Visit Details</div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="inv_purpose_id">Purpose of Visit *</label>
                            <select id="inv_purpose_id" name="purpose_id" required>
                                @foreach ($purposes as $purpose)
                                    <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_visitor_type_id">Visitor Type</label>
                            <select id="inv_visitor_type_id" name="visitor_type_id">
                                @foreach ($visitorTypes as $vType)
                                    <option value="{{ $vType->id }}">{{ $vType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_site_id">Branch / Location *</label>
                            <select id="inv_site_id" name="site_id" required onchange="filterHostEmployeesBySite(this.value); filterMeetingRoomsBySite(this.value);">
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_host_employee_id">Host Employee *</label>
                            <select id="inv_host_employee_id" name="host_employee_id" required>
                                <option value="">Select Host Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" data-site="{{ $employee->site_id }}">data-leaves="{{ $employee->leaves ? $employee->leaves->pluck('date')->map(fn($d) => $d->format('Y-m-d'))->join(',') : '' }}">{{ $employee->full_name }} ({{ $employee->department->name ?? 'General' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_department_id">Department</label>
                            <select id="inv_department_id" name="department_id">
                                <option value="">Auto-detect from host</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="inv_meeting_room">Meeting Room</label>
                            <input type="text" id="inv_meeting_room" name="meeting_room" list="meeting_rooms_list" placeholder="Search or select room..." autocomplete="off">
                            <datalist id="meeting_rooms_list">
                                @foreach ($meetingRooms as $mr)
                                    <option value="{{ $mr->name }}">{{ $mr->name }} ({{ $mr->floor?->name ? $mr->floor->name . ' · ' : '' }}Seats {{ $mr->capacity ?? 8 }})</option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="field span2">
                            <label for="inv_expected_visitor_count">Number of Visitors</label>
                            <input type="number" id="inv_expected_visitor_count" name="expected_visitor_count" value="1" min="1">
                        </div>
                    </div>

                    <!-- Section 5: Access & Requirements -->
                    <div class="section-label">Access & Requirements</div>
                    <div class="switchrow">
                        <span>Send confirmation email to visitor</span>
                        <label class="switch"><input type="checkbox" id="inv_send_confirmation_email" name="send_confirmation_email" value="1" checked><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Notify host upon arrival</span>
                        <label class="switch"><input type="checkbox" id="inv_notify_host_on_arrival" name="notify_host_on_arrival" value="1" checked><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Send QR code for self check-in</span>
                        <label class="switch"><input type="checkbox" id="inv_send_qr_self_checkin" name="send_qr_self_checkin" value="1" checked><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Requires NDA / Agreement signing</span>
                        <label class="switch"><input type="checkbox" id="inv_requires_nda" name="requires_nda" value="1"><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Wi-Fi guest access</span>
                        <label class="switch"><input type="checkbox" id="inv_wifi_guest_access" name="wifi_guest_access" value="1"><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Parking space required</span>
                        <label class="switch"><input type="checkbox" id="inv_parking_required" name="parking_required" value="1"><span class="slider"></span></label>
                    </div>
                    <div class="switchrow">
                        <span>Escort required</span>
                        <label class="switch"><input type="checkbox" id="inv_escort_required" name="escort_required" value="1"><span class="slider"></span></label>
                    </div>

                    <!-- Section 6: Additional Notes -->
                    <div class="section-label">Additional Notes</div>
                    <div class="field">
                        <label for="inv_special_instructions">Special Instructions</label>
                        <textarea id="inv_special_instructions" name="special_instructions" rows="2" placeholder="e.g. Visitor needs wheelchair access, bring laptop for demo, etc."></textarea>
                    </div>
                    <div class="field" style="margin-top: 10px;">
                        <label for="inv_items_to_bring">Items to Bring In</label>
                        <input type="text" id="inv_items_to_bring" name="items_to_bring" placeholder="e.g. Laptop, projector, tools">
                    </div>
                </div>

                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeInviteModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnRegisterNotify" style="display: inline-flex; align-items: center; gap: 8px;">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11.5 20.5 3.5 12.5 20l-2-6.5-6.5-2z"/><path d="M18.5 5.5 10.5 13.5"/></svg>
                        <span>Register & Notify</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- QR & Access Code Modal -->
    <div id="qrModal" class="modal-root">
        <div class="modal-backdrop" onclick="closeQrModal()"></div>
        <div class="modal-panel" style="max-width: 380px;">
            <div class="modal-head">
                <h3>Visitor Access Code</h3>
                <button type="button" class="btn btn-ghost btn-sm" onclick="closeQrModal()">✕</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <div class="badge-preview">
                    <div class="bp-top">Zapio VMS — Access Pass</div>
                    <div class="bp-name" id="qrModalVisitorName">John Doe</div>
                    <div class="bp-company" id="qrModalCompany">Acme Corp</div>
                    <div class="bp-qr" id="qrcodeCanvas"></div>
                    <div class="bp-code" id="qrModalCode">ZAP-0000</div>
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 14px;">
                    Share this code with the visitor, or have them quote it at reception upon arrival.
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeQrModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M7 8V4h10v4M6 17H4.5A1.5 1.5 0 0 1 3 15.5v-4A1.5 1.5 0 0 1 4.5 10h15a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1-1.5 1.5H18M7 14h10v6H7z"/></svg>
                    <span>Print Pass</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function setStatusFilter(status) {
            document.getElementById('statusInput').value = status;
            document.getElementById('filterForm').submit();
        }

        function filterHostEmployeesBySite(siteId) {
            const hostSelect = document.getElementById('inv_host_employee_id');
            if (!hostSelect) return;
            const options = hostSelect.querySelectorAll('option');
            const visitDate = document.getElementById('inv_visit_date') ? document.getElementById('inv_visit_date').value : '';

            options.forEach(opt => {
                if (!opt.value) return; // Keep "Select Host Employee"
                const empSite = opt.getAttribute('data-site');
                const leavesStr = opt.getAttribute('data-leaves') || '';
                const leaves = leavesStr ? leavesStr.split(',') : [];

                let show = true;
                if (siteId && siteId !== 'all' && empSite !== siteId) {
                    show = false;
                }
                if (visitDate && leaves.includes(visitDate)) {
                    show = false;
                }

                opt.style.display = show ? '' : 'none';
            });

            const selectedOpt = hostSelect.options[hostSelect.selectedIndex];
            if (selectedOpt && selectedOpt.style.display === 'none') {
                hostSelect.value = '';
            }
        }

        const allMeetingRoomsData = @json($meetingRooms);

        function filterMeetingRoomsBySite(siteId) {
            const dataList = document.getElementById('meeting_rooms_list');
            if (!dataList) return;
            dataList.innerHTML = '';
            
            allMeetingRoomsData.forEach(mr => {
                if (!siteId || siteId === 'all' || String(mr.site_id) === String(siteId)) {
                    const opt = document.createElement('option');
                    opt.value = mr.name;
                    const floorPart = mr.floor ? mr.floor.name + ' · ' : (mr.floor_name ? mr.floor_name + ' · ' : '');
                    opt.label = `${mr.name} (${floorPart}Seats ${mr.capacity || 8})`;
                    opt.textContent = `${mr.name} (${floorPart}Seats ${mr.capacity || 8})`;
                    dataList.appendChild(opt);
                }
            });
        }
