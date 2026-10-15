function openInviteModal() {
            const form = document.getElementById('inviteForm');
            const method = document.getElementById('invite_method');
            const title = document.getElementById('modalTitle');

            title.innerText = 'New Pre-registration';
            form.action = "{{ route('pre_registrations.store') }}";
            method.value = 'POST';
            form.reset();

            // Set default date to today and arrival to 10:00
            document.getElementById('inv_name').value = '';
            document.getElementById('inv_company_name').value = '';
            document.getElementById('inv_email').value = '';
            document.getElementById('inv_phone').value = '';
            document.getElementById('inv_phone_code').value = '971';
            document.getElementById('inv_id_number').value = '';
            document.getElementById('inv_visit_date').value = "{{ date('Y-m-d') }}";
            document.getElementById('inv_expected_arrival').value = "10:00";
            document.getElementById('inv_duration').value = "60";

            // Branch is no longer chosen manually (it comes from the host employee),
            // so list every host (minus anyone on leave) and every meeting room until a host is picked.
            document.getElementById('inv_department_id').value = '';
            filterHostEmployeesBySite('');
            filterMeetingRoomsBySite('');

            document.getElementById('invEditingId').value = '';
            document.getElementById('invEditingStatus').value = '';
            document.getElementById('invFormError').style.display = 'none';
            document.getElementById('inv_visit_date').min = "{{ date('Y-m-d') }}";
            setInviteScheduleLocked(false);
            document.getElementById('inviteModal').classList.add('open');
        }

        function openEditInviteModal(data) {
            const form = document.getElementById('inviteForm');
            const method = document.getElementById('invite_method');
            const title = document.getElementById('modalTitle');

            title.innerText = 'Edit Pre-registration';
            form.action = '/pre-registrations/' + data.id;
            method.value = 'PUT';

            document.getElementById('inv_name').value = data.name || ((data.first_name || '') + ' ' + (data.last_name || '')).trim();
            document.getElementById('inv_company_name').value = data.company_name || '';
            document.getElementById('inv_email').value = data.email || '';
            
            let phoneVal = data.phone || '';
            if (phoneVal.startsWith('+')) {
                let parts = phoneVal.replace('+', '').split(' ', 2);
                document.getElementById('inv_phone_code').value = parts[0] || '971';
                document.getElementById('inv_phone').value = parts[1] || '';
            } else {
                document.getElementById('inv_phone_code').value = '971';
                document.getElementById('inv_phone').value = phoneVal;
            }

            document.getElementById('inv_id_type_id').value = data.id_type_id || '';
            document.getElementById('inv_id_number').value = data.id_number || '';

            document.getElementById('inv_visit_date').value = data.visit_date || '';
            document.getElementById('inv_expected_arrival').value = data.expected_arrival || '10:00';
            document.getElementById('inv_expected_departure').value = data.expected_departure || '';
            document.getElementById('inv_duration').value = data.duration || 60;
            document.getElementById('inv_recurring').value = data.recurring || 'none';

            document.getElementById('inv_purpose_id').value = data.purpose_id || '';
            document.getElementById('inv_visitor_type_id').value = data.visitor_type_id || '';
            document.getElementById('inv_site_id').value = data.site_id || '';
            document.getElementById('inv_department_id').value = data.department_id || '';
            document.getElementById('inv_meeting_room').value = data.meeting_room || '';
            document.getElementById('inv_expected_visitor_count').value = data.expected_visitor_count || 1;

            filterHostEmployeesBySite('');
            filterMeetingRoomsBySite(data.site_id);
            // Setting .value does not fire onchange, so the saved department stays as stored.
            document.getElementById('inv_host_employee_id').value = data.host_employee_id || '';

            document.getElementById('inv_send_confirmation_email').checked = !!data.send_confirmation_email;
            document.getElementById('inv_notify_host_on_arrival').checked = !!data.notify_host_on_arrival;
            document.getElementById('inv_send_qr_self_checkin').checked = !!data.send_qr_self_checkin;
            document.getElementById('inv_requires_nda').checked = !!data.requires_nda;
            document.getElementById('inv_wifi_guest_access').checked = !!data.wifi_guest_access;
            document.getElementById('inv_parking_required').checked = !!data.parking_required;
            document.getElementById('inv_escort_required').checked = !!data.escort_required;

            document.getElementById('inv_special_instructions').value = data.special_instructions || '';
            document.getElementById('inv_items_to_bring').value = data.items_to_bring || '';

            document.getElementById('invEditingId').value = data.id || '';
            document.getElementById('invEditingStatus').value = data.status || '';
            document.getElementById('invFormError').style.display = 'none';
            // past dates can't be chosen; an existing past date may stay as it is
            const todayStr = "{{ date('Y-m-d') }}";
            document.getElementById('inv_visit_date').min = (data.visit_date && data.visit_date < todayStr) ? data.visit_date : todayStr;
            setInviteScheduleLocked(['checked_in', 'checked_out'].includes(data.status));
            document.getElementById('inviteModal').classList.add('open');
        }

        // Arrival time & visit details are read-only once the visitor has checked in
        function setInviteScheduleLocked(locked) {
            const fs = document.getElementById('inv_locked_sections');
            const note = document.getElementById('inv_locked_notice');
            if (fs) fs.disabled = !!locked;
            if (note) note.style.display = locked ? 'flex' : 'none';
        }

        @if ((session('error') || $errors->any()) && old('_form') === 'invite')
        // The last pre-registration save was refused: reopen the form with what was typed and show why
        document.addEventListener('DOMContentLoaded', function () {
            const old = @json(request()->old());
            if (old._editing_invitation_id) {
                openEditInviteModal({ id: old._editing_invitation_id, status: old._editing_status || '' });
            } else {
                openInviteModal();
            }
            const set = (id, v) => { const el = document.getElementById(id); if (el && v !== undefined && v !== null) el.value = v; };
            set('inv_name', old.name);
            set('inv_company_name', old.company_name);
            set('inv_email', old.email);
            set('inv_phone_code', old.phone_code);
            set('inv_phone', old.phone);
            set('inv_id_type_id', old.id_type_id);
            set('inv_id_number', old.id_number);
            set('inv_visit_date', old.visit_date);
            set('inv_expected_arrival', old.expected_arrival);
            set('inv_expected_departure', old.expected_departure);
            set('inv_duration', old.duration);
            set('inv_recurring', old.recurring);
            set('inv_purpose_id', old.purpose_id);
            set('inv_visitor_type_id', old.visitor_type_id);
            set('inv_host_employee_id', old.host_employee_id);
            set('inv_department_id', old.department_id);
            set('inv_meeting_room', old.meeting_room);
            set('inv_expected_visitor_count', old.expected_visitor_count);
            set('inv_special_instructions', old.special_instructions);
            set('inv_items_to_bring', old.items_to_bring);
            ['send_confirmation_email', 'notify_host_on_arrival', 'send_qr_self_checkin', 'requires_nda', 'wifi_guest_access', 'parking_required', 'escort_required'].forEach(k => { const el = document.getElementById('inv_' + k); if (el) el.checked = !!old[k]; });
            const err = document.getElementById('invFormError');
            if (err) { err.innerText = @json(session('error') ?? $errors->first()); err.style.display = ''; }
        });
        @endif

        function closeInviteModal() {
            document.getElementById('inviteModal').classList.remove('open');
        }

        // Host employee drives Department (auto-select) and the hidden Branch value.
        function onInviteHostChange() {
            const hostSelect = document.getElementById('inv_host_employee_id');
            const deptSelect = document.getElementById('inv_department_id');
            const siteInput = document.getElementById('inv_site_id');
            const opt = hostSelect.options[hostSelect.selectedIndex];

            if (!opt || !opt.value) {
                deptSelect.value = '';
                filterMeetingRoomsBySite('');
                return;
            }

            // Department = the host's department ('' = "Auto-detect from host" if the host has none)
            deptSelect.value = opt.getAttribute('data-department') || '';

            // Branch = the host's branch (kept in the hidden site_id field)
            const empSite = opt.getAttribute('data-site');
            if (empSite) siteInput.value = empSite;

            filterMeetingRoomsBySite(siteInput.value);
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

        var allMeetingRoomsData = @json($meetingRooms);

        function filterMeetingRoomsBySite(siteId) {
            const dataList = document.getElementById('meeting_rooms_list');
            if (!dataList) return;
            dataList.innerHTML = '';
            
            allMeetingRoomsData.forEach(mr => {
                if (!siteId || siteId === 'all' || String(mr.site_id) === String(siteId)) {
                    const opt = document.createElement('option');
                    // Handle both array and object formats for mr
                    const mrName = typeof mr.name !== 'undefined' ? mr.name : mr['name'];
                    const mrCapacity = typeof mr.capacity !== 'undefined' ? mr.capacity : mr['capacity'];
                    const mrFloorName = mr.floor ? mr.floor.name : (mr.floor_name ? mr.floor_name : '');
                    
                    opt.value = mrName;
                    const floorPart = mrFloorName ? mrFloorName + ' - ' : '';
                    opt.label = mrName + ' (' + floorPart + 'Seats ' + (mrCapacity || 8) + ')';
                    opt.textContent = mrName + ' (' + floorPart + 'Seats ' + (mrCapacity || 8) + ')';
                    dataList.appendChild(opt);
                }
            });
        }
