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

            // Trigger host & room filtering
            const siteId = document.getElementById('inv_site_id').value;
            filterHostEmployeesBySite(siteId);
            filterMeetingRoomsBySite(siteId);

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

            filterHostEmployeesBySite(data.site_id);
            filterMeetingRoomsBySite(data.site_id);
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

            document.getElementById('inviteModal').classList.add('open');
        }

        function closeInviteModal() {
            document.getElementById('inviteModal').classList.remove('open');
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
