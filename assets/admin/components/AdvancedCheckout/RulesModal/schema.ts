import * as Yup from 'yup'

const separators = /[$%|]/

const RulesSchema = Yup.object().shape({
  rules: Yup.array()
    .of(
      Yup.object().shape({
        field: Yup.string()
          .required()
          .test(
            'Het veld mag geen $, | en % karakters bevatten.',
            (value) => Boolean(value) && !separators.test(value as string),
          ),
        comparator: Yup.string()
          .required()
          .test(
            'Het veld mag geen $, | en % karakters bevatten.',
            (value) => Boolean(value) && !separators.test(value as string),
          ),
        value: Yup.string()
          .required()
          .test(
            'Het veld mag geen $, | en % karakters bevatten.',
            (value) => Boolean(value) && !separators.test(value as string),
          ),
      }),
    )
    .min(1),
})

export default RulesSchema
