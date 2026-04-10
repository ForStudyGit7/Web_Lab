export default defineEventHandler(() => {
  return {
    planName: 'Team - Annual',
    price: 207.50,
    billedYearly: 2490.00,
    savings: 498,
    features: [
      { text: 'Primary user + 2 free team members', subtext: '(extra team members for $25/month)' },
      { text: 'Save unlimited properties' },
      { text: '50,000 exports', bold: true, subtext: '(additional exports at $0.01 each)' },
      { text: '1,000 free skip traces', subtext: '(additional skip tracing at $0.08 each)' },
      { text: 'Imports $0.01' },
      { text: 'FREE daily product trainings and support' },
      { text: 'Full suite of next-gen investing tools' },
      { text: 'Industry first AI powered comp tool' },
      { text: 'Includes dedicated support agent' }
    ]
  }
})
