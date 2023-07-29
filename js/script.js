let messageHistory = [];
let assistantMessages = []; // 配列を追加

async function appendAssistantResponse(assistantMessage) {
  assistantMessages.push({ role: "assistant", content: assistantMessage });
}

$("#chat-form").on("submit", async function (event) {
  event.preventDefault();
  const userMessage = $("#chat-input").val();
  $("#chat-history").append('<p class="you">' + userMessage + "</p>");

  const existSystemMessage = messageHistory.findIndex(
    (message) => message.role === "system"
  );

  if (existSystemMessage === -1) {
    messageHistory.unshift({
      role: "system",
      content:
        "役割】あなたはAI編集者です。私は起業家です。あなたは、親しみやすいキャラクターで、私をいつも励ましてください。【目標】 あなたは、私が投資家向けピッチのストーリーを書くサポートをしてください。対話を通じて、私がストーリーをまとめることが、目標です【フロー】１）あなたはまず『こんにちは！　わたしはあなたのストーリー作りを手伝うAI編集者です。どんな事業を考えていますか？　ごく簡単でいいので教えてください』と私に話しかけてください。２）私の回答を受けて、なぜそう思ったのか、原体験を思い出させるような質問をさまざまな角度からたくさんしてください。３）私が抽象的な回答をしたときは、より具体的な描写になるよう導いてください。４）あなたが5回回答したら、6回目の回答の時に『あなたは以下のような原体験を持っているのですね。まだやり取りを続けますか？』という文章と、これまでのやり取りを250字で要約した文章を送信してください。【ルール】    ・一度の回答は250文字以内にしてください。・質問は一度にひとつずつにしてください。・抽象的な回答が続くようであれば、あなたは「たとえば」と具体的な例を示しつつ、質問をしてみてください。",
    });
  }

  messageHistory.push({ role: "user", content: userMessage });

  // 直近6回の会話履歴のみを保持する
  if (messageHistory.length > 6) {
    messageHistory = messageHistory.slice(-6);
  }

  const formData = $(this).serialize();
  const url = "https://api.openai.com/v1/chat/completions";
  const response = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: "Bearer ",
    },
    body: JSON.stringify({
      model: "gpt-3.5-turbo",
      stream: true,
      messages: messageHistory,
    }),
  });

  if (!response.ok) {
    console.error("Error:", await response.text());
    return;
  }

  $("#chat-input").val("");
  $("#chat-input").focus();

  const reader = response.body.getReader();
  const textDecoder = new TextDecoder();
  let buffer = "";

  while (true) {
    const { value, done } = await reader.read();

    if (done) {
      break;
    }

    buffer += textDecoder.decode(value, { stream: true });

    while (true) {
      const newlineIndex = buffer.indexOf("\n");
      if (newlineIndex === -1) {
        break;
      }

      const line = buffer.slice(0, newlineIndex);
      buffer = buffer.slice(newlineIndex + 1);

      if (line.startsWith("data:")) {
        if (line.includes("[DONE]")) {
          $("#chat-history").append("<hr>");
          const res = assistantMessages.reduce((acc, cur) => {
            return acc + cur.content;
          }, "");
          messageHistory.push({ role: "assistant", content: res });
          console.log(messageHistory);
          return;
        }

        const jsonData = JSON.parse(line.slice(5));

        if (
          jsonData.choices &&
          jsonData.choices[0].delta &&
          jsonData.choices[0].delta.content
        ) {
          const assistantMessage = jsonData.choices[0].delta.content;
          $("#chat-history").append("" + assistantMessage + "");
          await appendAssistantResponse(assistantMessage);
        }
      }
    }
  }
});

/******************************************
 * チャットウィンドウのスクロール
 ******************************************/
const chatWindow = document.getElementById("chat-window");
function scrollChatWindow() {
  const chatWindowHeight = chatWindow.clientHeight;
  const chatWindowScrollHeight = chatWindow.scrollHeight;
  const chatWindowTextHeight = chatWindowScrollHeight - chatWindow.scrollTop;
  if (chatWindowTextHeight > chatWindowHeight) {
    chatWindow.scrollTop = chatWindowScrollHeight;
  }
}
chatWindow.addEventListener("DOMNodeInserted", scrollChatWindow);

/******************************************
 * ボタンクリック時の処理
 ******************************************/
$("#ref").on("click", async function () {
  var storyareaText = $("#storyarea").text(); // storyareaのテキストを取得
  var chatHistoryText = $("#chat-history").text(); // chat-historyのテキストを取得
  var combinedText = storyareaText + chatHistoryText; // テキストを結合

  const res = await summarizeText(combinedText); // テキストを要約

  if (res.ok) {
    alert("スライドの生成に成功しました！");
    $("#storyarea").val("スライドの生成に成功しました！"); // 要約したテキストをstoryareaにセット
  }
});

async function summarizeText(text) {
  const response = await fetch("api/summarize.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "text=" + encodeURIComponent(text),
  });

  console.log(response);
  return response;
}
