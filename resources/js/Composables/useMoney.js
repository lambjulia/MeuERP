import { computed } from 'vue';

export function useMoney() {
    const format = (value) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(Number(value || 0));
    };

    return { format };
}
