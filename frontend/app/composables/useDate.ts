export function useDate() {
  const pad = (n: number) => n.toString().padStart(2, '0')

  const getToday = (): string => {
    const d = new Date()
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
  }

  return { getToday }
}
