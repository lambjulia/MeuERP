export const PurchaseStatus = {
    draft: { value: 'draft', label: 'Rascunho', color: 'warn' },
    confirmed: { value: 'confirmed', label: 'Confirmada', color: 'success' },
    cancelled: { value: 'cancelled', label: 'Cancelada', color: 'danger' },
};

export const SaleStatus = {
    draft: { value: 'draft', label: 'Rascunho', color: 'warn' },
    confirmed: { value: 'confirmed', label: 'Confirmada', color: 'success' },
    cancelled: { value: 'cancelled', label: 'Cancelada', color: 'danger' },
};

export const FinancialStatus = {
    pending: { value: 'pending', label: 'Pendente', color: 'warn' },
    paid: { value: 'paid', label: 'Pago', color: 'success' },
    overdue: { value: 'overdue', label: 'Vencido', color: 'danger' },
    cancelled: { value: 'cancelled', label: 'Cancelado', color: 'secondary' },
};

export const StockMovementType = {
    in: { value: 'in', label: 'Entrada', color: 'success' },
    out: { value: 'out', label: 'Saída', color: 'danger' },
    adjustment: { value: 'adjustment', label: 'Ajuste', color: 'info' },
};
