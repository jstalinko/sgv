import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function humanizeNumber(num: number) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + ' Juta';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + ' Ribu';
    } else {
        return num;
    }
}