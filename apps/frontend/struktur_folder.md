# Struktur Folder Frontend

Dokumen ini mencatat struktur folder scaffold terbaru di `apps/frontend/`.

```text
apps/frontend/
├── __tests__/
│   ├── integration/
│   │   └── features/
│   │       └── .gitkeep
│   ├── mocks/
│   │   ├── data.ts
│   │   └── handlers.ts
│   └── unit/
│       ├── components/
│       │   └── .gitkeep
│       ├── hooks/
│       │   └── .gitkeep
│       └── utils/
│           └── .gitkeep
├── app/
│   ├── (auth)/
│   │   ├── login/
│   │   │   └── page.tsx
│   │   ├── register/
│   │   │   └── page.tsx
│   │   └── layout.tsx
│   ├── (main)/
│   │   ├── badges/
│   │   │   └── page.tsx
│   │   ├── budgets/
│   │   │   ├── [id]/
│   │   │   │   └── page.tsx
│   │   │   └── page.tsx
│   │   ├── categories/
│   │   │   └── page.tsx
│   │   ├── dashboard/
│   │   │   └── page.tsx
│   │   ├── insights/
│   │   │   └── page.tsx
│   │   ├── notifications/
│   │   │   └── page.tsx
│   │   ├── profile/
│   │   │   └── page.tsx
│   │   ├── reports/
│   │   │   └── page.tsx
│   │   ├── transactions/
│   │   │   ├── [id]/
│   │   │   │   └── page.tsx
│   │   │   └── page.tsx
│   │   └── layout.tsx
│   ├── error.tsx
│   ├── loading.tsx
│   ├── not-found.tsx
│   └── providers.tsx
├── components/
│   ├── badges/
│   │   ├── badge-card.tsx
│   │   ├── badge-grid.tsx
│   │   ├── index.ts
│   │   └── level-progress.tsx
│   ├── budgets/
│   │   ├── budget-card.tsx
│   │   ├── budget-form.tsx
│   │   ├── budget-progress.tsx
│   │   ├── budget-summary.tsx
│   │   └── index.ts
│   ├── categories/
│   │   ├── category-badge.tsx
│   │   ├── category-form.tsx
│   │   ├── category-list.tsx
│   │   └── index.ts
│   ├── common/
│   │   ├── confirm-dialog.tsx
│   │   ├── currency-input.tsx
│   │   ├── date-range-picker.tsx
│   │   ├── empty-state.tsx
│   │   ├── error-boundary.tsx
│   │   ├── index.ts
│   │   ├── loading-spinner.tsx
│   │   └── pagination.tsx
│   ├── dashboard/
│   │   ├── balance-card.tsx
│   │   ├── budget-status.tsx
│   │   ├── expense-chart.tsx
│   │   ├── index.ts
│   │   ├── recent-transactions.tsx
│   │   └── upcoming-bills.tsx
│   ├── insights/
│   │   ├── index.ts
│   │   ├── insight-card.tsx
│   │   ├── insight-list.tsx
│   │   └── recommendation-item.tsx
│   ├── layout/
│   │   ├── header.tsx
│   │   ├── index.ts
│   │   ├── mobile-nav.tsx
│   │   └── sidebar.tsx
│   ├── notifications/
│   │   ├── index.ts
│   │   ├── notification-item.tsx
│   │   └── notification-list.tsx
│   ├── profile/
│   │   ├── avatar-upload.tsx
│   │   ├── index.ts
│   │   ├── password-form.tsx
│   │   ├── profile-form.tsx
│   │   └── settings-form.tsx
│   ├── reports/
│   │   ├── export-button.tsx
│   │   ├── index.ts
│   │   ├── report-chart.tsx
│   │   ├── report-filters.tsx
│   │   └── report-summary.tsx
│   ├── transactions/
│   │   ├── index.ts
│   │   ├── receipt-upload.tsx
│   │   ├── transaction-filters.tsx
│   │   ├── transaction-form.tsx
│   │   ├── transaction-item.tsx
│   │   └── transaction-list.tsx
│   ├── ui/
│   │   ├── button.tsx
│   │   ├── card.tsx
│   │   ├── dialog.tsx
│   │   ├── dropdown-menu.tsx
│   │   ├── input.tsx
│   │   ├── label.tsx
│   │   ├── select.tsx
│   │   ├── table.tsx
│   │   └── tabs.tsx
├── constants/
│   ├── badges.ts
│   ├── categories.ts
│   ├── config.ts
│   ├── index.ts
│   └── routes.ts
├── features/
│   ├── auth/
│   │   ├── hooks/
│   │   │   ├── use-login.ts
│   │   │   ├── use-logout.ts
│   │   │   └── use-register.ts
│   │   ├── services/
│   │   │   └── auth.service.ts
│   │   ├── types/
│   │   │   └── auth.types.ts
│   │   └── index.ts
│   ├── badges/
│   │   ├── hooks/
│   │   │   ├── use-all-badges.ts
│   │   │   └── use-badges.ts
│   │   ├── services/
│   │   │   └── badge.service.ts
│   │   ├── types/
│   │   │   └── badge.types.ts
│   │   └── index.ts
│   ├── budgets/
│   │   ├── hooks/
│   │   │   ├── use-budget-mutations.ts
│   │   │   ├── use-budget.ts
│   │   │   ├── use-budgets.ts
│   │   │   └── use-current-budgets.ts
│   │   ├── services/
│   │   │   └── budget.service.ts
│   │   ├── types/
│   │   │   └── budget.types.ts
│   │   └── index.ts
│   ├── categories/
│   │   ├── hooks/
│   │   │   ├── use-categories.ts
│   │   │   ├── use-category-mutations.ts
│   │   │   └── use-category.ts
│   │   ├── services/
│   │   │   └── category.service.ts
│   │   ├── types/
│   │   │   └── category.types.ts
│   │   └── index.ts
│   ├── insights/
│   │   ├── hooks/
│   │   │   ├── use-insights.ts
│   │   │   ├── use-latest-insight.ts
│   │   │   └── use-mark-insight-read.ts
│   │   ├── services/
│   │   │   └── insight.service.ts
│   │   ├── types/
│   │   │   └── insight.types.ts
│   │   └── index.ts
│   ├── notifications/
│   │   ├── hooks/
│   │   │   ├── use-notification-mutations.ts
│   │   │   ├── use-notifications.ts
│   │   │   └── use-unread-count.ts
│   │   ├── services/
│   │   │   └── notification.service.ts
│   │   ├── types/
│   │   │   └── notification.types.ts
│   │   └── index.ts
│   ├── profile/
│   │   ├── hooks/
│   │   │   ├── use-profile.ts
│   │   │   ├── use-update-password.ts
│   │   │   ├── use-update-profile.ts
│   │   │   └── use-update-settings.ts
│   │   ├── services/
│   │   │   └── profile.service.ts
│   │   ├── types/
│   │   │   └── profile.types.ts
│   │   └── index.ts
│   ├── reports/
│   │   ├── hooks/
│   │   │   ├── use-custom-report.ts
│   │   │   ├── use-export-report.ts
│   │   │   ├── use-monthly-report.ts
│   │   │   └── use-weekly-report.ts
│   │   ├── services/
│   │   │   └── report.service.ts
│   │   ├── types/
│   │   │   └── report.types.ts
│   │   └── index.ts
│   └── transactions/
│       ├── hooks/
│       │   ├── use-create-transaction.ts
│       │   ├── use-delete-transaction.ts
│       │   ├── use-transaction.ts
│       │   ├── use-transactions.ts
│       │   └── use-update-transaction.ts
│       ├── services/
│       │   └── transaction.service.ts
│       ├── types/
│       │   └── transaction.types.ts
│       └── index.ts
├── hooks/
│   ├── index.ts
│   ├── use-click-outside.ts
│   ├── use-debounce.ts
│   ├── use-local-storage.ts
│   ├── use-media-query.ts
│   └── use-toast.ts
├── lib/
│   ├── api/
│   │   ├── api-client.ts
│   │   ├── axios-instance.ts
│   │   └── index.ts
│   ├── form/
│   │   ├── schemas/
│   │   │   ├── auth.schema.ts
│   │   │   ├── budget.schema.ts
│   │   │   ├── category.schema.ts
│   │   │   ├── index.ts
│   │   │   ├── profile.schema.ts
│   │   │   └── transaction.schema.ts
│   │   └── index.ts
│   ├── utils/
│   │   ├── formatter.ts
│   │   ├── index.ts
│   │   └── validators.ts
│   └── index.ts
├── public/
│   ├── fonts/
│   ├── images/
│   │   ├── badges/
│   │   └── icons/
├── services/
│   ├── export.service.ts
│   ├── index.ts
│   └── upload.service.ts
├── stores/
│   ├── auth.store.ts
│   ├── filter.store.ts
│   ├── index.ts
│   └── ui.store.ts
├── styles/
│   ├── globals.css
│   └── themes.css
├── types/
│   ├── api.types.ts
│   ├── common.types.ts
│   ├── global.d.ts
│   └── index.ts
└── utils/
    ├── currency.utils.ts
    ├── date.utils.ts
    ├── error.utils.ts
    └── index.ts
```

