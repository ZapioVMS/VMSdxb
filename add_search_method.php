<?php

$file = 'app/Http/Controllers/VisitorLogController.php';
$content = file_get_contents($file);

$method = <<<'PHP'

    /**
     * Search past visitors for auto-suggest in the check-in form.
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $orgId = session('organization_id');

        $visitors = \App\Models\Visitor::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('organization_id', $orgId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('id_number', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        $results = $visitors->map(function ($visitor) {
            return [
                'id' => $visitor->id,
                'name' => $visitor->full_name,
                'phone' => $visitor->phone ? '+' . $visitor->phone_code . ' ' . $visitor->phone : '',
                'email' => $visitor->email,
                'company' => $visitor->company_name,
                'data' => [
                    'first_name' => $visitor->first_name,
                    'last_name' => $visitor->last_name,
                    'email' => $visitor->email,
                    'phone' => $visitor->phone,
                    'phone_code' => $visitor->phone_code,
                    'id_type_id' => $visitor->id_type_id,
                    'id_number' => $visitor->id_number,
                    'nationality_id' => $visitor->nationality_id,
                    'company_name' => $visitor->company_name,
                ]
            ];
        });

        return response()->json($results);
    }
}
PHP;

$content = preg_replace('/}\s*$/', $method, $content);
file_put_contents($file, $content);
echo "Added search method.\n";
