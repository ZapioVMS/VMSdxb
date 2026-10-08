<?php

$file = 'resources/views/visitors/checkin.blade.php';
$content = file_get_contents($file);

// Find the <select name="phone_code"> block and replace it
$pattern = '/<select name="phone_code">.*?<\/select>/s';

$newSelect = <<<'HTML'
                                        <select name="phone_code">
                                            @php
                                                $curatedIsoCodes = ['AE','SA','QA','KW','BH','OM','JO','LB','IQ','EG','YE','SY','PS','IL','TR','IR','US','CA','GB','IE','FR','DE','ES','PT','IT','NL','BE','CH','AT','SE','NO','DK','FI','PL','CZ','GR','RO','RU','UA','IN','PK','BD','LK','NP','CN','JP','KR','HK','SG','MY','ID','PH','TH','VN','TW','AU','NZ','ZA','NG','KE','GH','MA','DZ','TN','ET','SD','BR','MX','AR','CO','CL','PE','AF','AZ','GE','AM','KZ','UZ'];
                                                $curatedCountries = $countries->whereIn('iso_code', $curatedIsoCodes)->sort(function($a, $b) {
                                                    if ($a->iso_code === 'AE') return -1;
                                                    if ($b->iso_code === 'AE') return 1;
                                                    return strcmp($a->name, $b->name);
                                                });
                                            @endphp
                                            @foreach($curatedCountries as $c)
                                                @if($c->isd_code)
                                                    <option value="{{ $c->isd_code }}" {{ old('phone_code', '971') == $c->isd_code ? 'selected' : '' }}>+{{ $c->isd_code }} ({{ $c->iso_code }})</option>
                                                @endif
                                            @endforeach
                                        </select>
HTML;

$content = preg_replace($pattern, $newSelect, $content);

file_put_contents($file, $content);
echo "Phone code select updated to exactly match template.\n";
