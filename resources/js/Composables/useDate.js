export function useDate() {
    const format = (value) => {
        if (!value) return '—';
        return new Date(value).toLocaleDateString('pt-BR');
    };

    const formatDateTime = (value) => {
        if (!value) return '—';
        return new Date(value).toLocaleString('pt-BR');
    };

    return { format, formatDateTime };
}
