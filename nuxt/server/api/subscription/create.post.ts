export default defineEventHandler(async (event) => {
  const body = await readBody(event)


  console.log('Отримано дані підписки:', body)

  return {
    status: 'success',
    message: 'Підписка успішно створена!'
  }
})
