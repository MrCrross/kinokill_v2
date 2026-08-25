export const messages = {
    ru: {
        'auth.email': 'Email',
        'auth.password': 'Пароль',
        'auth.password_confirmation': 'Подтверждение пароля',
        'auth.name': 'Имя',

        'auth.login.title': 'Вход',
        'auth.login.remember': 'Запомнить меня',
        'auth.login.forgot_password': 'Забыли пароль?',
        'auth.login.submit': 'Войти',

        'auth.register.title': 'Регистрация',
        'auth.register.already_registered': 'Уже зарегистрированы?',
        'auth.register.submit': 'Зарегистрироваться',

        'auth.forgot_password.title': 'Восстановление пароля',
        'auth.forgot_password.description':
            'Забыли пароль? Ничего страшного. Просто укажите email, и мы отправим вам ссылку для сброса пароля.',
        'auth.forgot_password.submit': 'Отправить ссылку для сброса пароля',

        'auth.reset_password.title': 'Сброс пароля',
        'auth.reset_password.submit': 'Сбросить пароль',

        'auth.confirm_password.title': 'Подтверждение пароля',
        'auth.confirm_password.description':
            'Это защищённый раздел приложения. Пожалуйста, подтвердите пароль перед продолжением.',
        'auth.confirm_password.submit': 'Подтвердить',

        'auth.verify_email.title': 'Подтверждение email',
        'auth.verify_email.description':
            'Спасибо за регистрацию! Прежде чем начать, подтвердите свой email, перейдя по ссылке, которую мы вам отправили. Если письмо не пришло, мы с радостью отправим ещё одно.',
        'auth.verify_email.link_sent':
            'Новая ссылка для подтверждения отправлена на email, указанный при регистрации.',
        'auth.verify_email.submit': 'Отправить письмо повторно',
        'auth.verify_email.logout': 'Выйти',
    },
    en: {
        'auth.email': 'Email',
        'auth.password': 'Password',
        'auth.password_confirmation': 'Confirm password',
        'auth.name': 'Name',

        'auth.login.title': 'Log in',
        'auth.login.remember': 'Remember me',
        'auth.login.forgot_password': 'Forgot your password?',
        'auth.login.submit': 'Log in',

        'auth.register.title': 'Register',
        'auth.register.already_registered': 'Already registered?',
        'auth.register.submit': 'Register',

        'auth.forgot_password.title': 'Forgot Password',
        'auth.forgot_password.description':
            'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.',
        'auth.forgot_password.submit': 'Email Password Reset Link',

        'auth.reset_password.title': 'Reset Password',
        'auth.reset_password.submit': 'Reset Password',

        'auth.confirm_password.title': 'Confirm Password',
        'auth.confirm_password.description':
            'This is a secure area of the application. Please confirm your password before continuing.',
        'auth.confirm_password.submit': 'Confirm',

        'auth.verify_email.title': 'Email Verification',
        'auth.verify_email.description':
            "Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.",
        'auth.verify_email.link_sent':
            'A new verification link has been sent to the email address you provided during registration.',
        'auth.verify_email.submit': 'Resend Verification Email',
        'auth.verify_email.logout': 'Log out',
    },
} as const;

export type MessageKey = keyof (typeof messages)['ru'];
