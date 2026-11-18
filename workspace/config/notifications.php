<?php

return [
    'email' => [
        'default_template' => 'modules.core.notifications.email.generic',
        'preference_categories' => [
            'leave_sick' => [
                'label' => 'Cuti & sakit',
                'description' => 'Pemberitahuan pengajuan dan perubahan status cuti atau sakit.',
            ],
            'overtime' => [
                'label' => 'Lembur',
                'description' => 'Pemberitahuan pengajuan dan perubahan status lembur.',
            ],
            'reimbursement' => [
                'label' => 'Reimbursement',
                'description' => 'Pemberitahuan pengajuan dan perubahan status reimbursement.',
            ],
            'cash_advance' => [
                'label' => 'Cash advance',
                'description' => 'Pemberitahuan pengajuan dan perubahan status cash advance.',
            ],
            'other' => [
                'label' => 'Notifikasi lainnya',
                'description' => 'Email notifikasi yang tidak termasuk kategori HRIS di atas.',
            ],
        ],
        'templates' => [
            'hris' => 'modules.core.notifications.email.generic',
        ],
    ],
];
