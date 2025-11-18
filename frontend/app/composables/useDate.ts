export function useDate() {
  const pad = (n: number) => n.toString().padStart(2, '0')

  const getToday = (): string => {
    const d = new Date()
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
  }

  const formatDate = (dateString: string): string => {
    const d = new Date(dateString + 'T00:00:00')
    return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })
  }

  const getDateAgo = (daysAgo: number): string => {
    const d = new Date()
    d.setDate(d.getDate() - daysAgo)
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
  }

  const isToday = (dateString: string): boolean => {
    return dateString === getToday()
  }

  const compareDates = (date1: string, date2: string): number => {
    const a = new Date(date1 + 'T00:00:00').getTime()
    const b = new Date(date2 + 'T00:00:00').getTime()
    if (a < b) return -1
    if (a > b) return 1
    return 0
  }

  return { getToday, formatDate, getDateAgo, isToday, compareDates }
}
