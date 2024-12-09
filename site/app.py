from flask import Flask, request, render_template, jsonify
import requests
import json
from urllib.parse import quote

app = Flask(__name__)

# Replace with your actual keys
vectara_key = "zut__oZQMNcHTl6aadKpWhXfw_236BWI5bEBAg_rfg"
corpus_key = "Events"

def text_to_url_query(text):
    """
    Convert plain text into URL query text.
    """
    query_text = quote(text)
    return query_text

def ask_query(query):
    q = text_to_url_query(query)

    payload = json.dumps(
        {
            "query": query,
            "search": {
                "metadata_filter": "",
                "lexical_interpolation": 0.005,
                "custom_dimensions": {},
                "offset": 0,
                "limit": 10,
                "context_configuration": {
                    "sentences_before": 2,
                    "sentences_after": 2,
                    "start_tag": "%START_SNIPPET%",
                    "end_tag": "%END_SNIPPET%"
                },
                "reranker": {
                    "type": "customer_reranker",
                    "reranker_id": "rnk_272725719"
                }
            },
            "stream_response": False,
            "generation": {
                "generation_preset_name": "mockingbird-1.0-2024-07-16",
                "max_used_search_results": 5,
                "response_language": "eng",
                "enable_factual_consistency_score": False
            },
        }
    )
    headers = {
        'Accept': 'application/json',
        'x-api-key': vectara_key
    }

    url = f"https://api.vectara.io/v2/corpora/{corpus_key}/query?query={q}"
    response = requests.request("POST", url, headers=headers, data=payload)
    output = json.loads(response.text)

    return output["summary"]

# # Route for input and output
# @app.route('/', methods=['GET', 'POST'])
# def home():
#     result = None
#     if request.method == 'POST':
#         query = request.form.get('query')
#         if query:
#             try:
#                 result = ask_query(query)
#             except Exception as e:
#                 result = f"Error: {str(e)}"
    
#     # HTML template for input box and result display
#     html = """
#     <!doctype html>
#     <html>
#         <head>
#             <title>Query Input</title>
#         </head>
#         <body>
#             <h1>Enter your query</h1>
#             <form method="post">
#                 <label for="query">Query:</label>
#                 <input type="text" id="query" name="query" required>
#                 <button type="submit">Submit</button>
#             </form>
#             {% if result %}
#                 <h2>Result:</h2>
#                 <p>{{ result }}</p>
#             {% endif %}
#         </body>
#     </html>
#     """
#     return render_template_string(html, result=result)

# if __name__ == '__main__':
#     app.run(debug=True)

@app.route("/")
def chat():
    """
    Render the chat interface.
    """
    return render_template("chat.html")

@app.route("/send_message", methods=["POST"])
def send_message():
    """
    Handle messages sent from the chat interface.
    """
    user_message = request.json.get("message", "")
    if not user_message:
        return jsonify({"response": "Please enter a message."})

    try:
        bot_response = ask_query(user_message)
        return jsonify({"response": bot_response})
    except Exception as e:
        return jsonify({"response": f"Error: {str(e)}"})

if __name__ == "__main__":
    app.run(debug=True)