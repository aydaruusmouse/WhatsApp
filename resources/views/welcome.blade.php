<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Chatbot Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>WhatsApp Chatbot Test</h2>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="/whatsapp/test">
                    @csrf
                    <div class="mb-3">
                        <label for="message" class="form-label">Type a Message:</label>
                        <input type="text" class="form-control" id="message" name="message" placeholder="Enter your message" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
        @if (session('response'))
            <div class="alert alert-success mt-3">
                {!! session('response') !!}
            </div>
        @endif
    </div>
</body>
</html>
