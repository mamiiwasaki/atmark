<pre>
package com.example.startingpoint;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;


public class Email extends Activity implements OnClickListener{
	EditText personsEmail, intro, personsName, stupidThings, hatefulAction,	outro;
	Button sendEmail;
	String emailAdd, beginning, name, stupidAction, hatefulAct, out;

	public void onCreate(Bundle savedInstanceState){
		super.onCreate(savedInstanceState);
		setContentView(R.layout.email);
		inisialize();
		sendEmail.setOnClickListener(this);
	}

	private void inisialize() {
		// TODO 自動生成されたメソッド・スタブ
		personsEmail = (EditText) findViewById(R.id.etEmails);
		intro = (EditText) findViewById(R.id.etIntro);
		personsName = (EditText) findViewById(R.id.etName);
		stupidThings = (EditText) findViewById(R.id.etThings);
		hatefulAction = (EditText) findViewById(R.id.etAction);
		outro = (EditText) findViewById(R.id.etOutro);
		sendEmail = (Button) findViewById(R.id.bSentEmail);
	}

	@Override
	public void onClick(View arg0) {
		// TODO 自動生成されたメソッド・スタブ
		convertEditTextVarsIntoStringsAndYesThisIsAMethodWeCreated();
		String emailaddress[] = { emailAdd };
		String message = "Well hello "
				+ name
				+ " I just wanted to say "
				+ beginning
				+ ".  Not only that but I hate when you "
				+ stupidAction
				+ ", that just really makes me crazy.  I just want to make you "
				+ hatefulAct
				+ ".  Welp, thats all I wanted to chit-chatter about, oh and"
				+ out
				+ ".  Oh also if you get bored you should check out www.mybringback.com"
				+ '\n' + "PS. I think I love you...   :(";

		Intent emailIntent = new Intent(android.content.Intent.ACTION_SEND);
		emailIntent.putExtra(android.content.Intent.EXTRA_EMAIL, emailaddress);
		emailIntent.putExtra(android.content.Intent.EXTRA_SUBJECT, "I hate you!");
		emailIntent.putExtra(android.content.Intent.EXTRA_TEXT, message);
		emailIntent.setType("plain/text");
		startActivity(emailIntent);

	}

	private void convertEditTextVarsIntoStringsAndYesThisIsAMethodWeCreated() {
		// TODO 自動生成されたメソッド・スタブ
		emailAdd = personsEmail.getText().toString();
		beginning = intro.getText().toString();
		name = personsName.getText().toString();
		stupidAction = stupidThings.getText().toString();
		hatefulAct = hatefulAction.getText().toString();
		out = outro.getText().toString();
	}

	@Override
	protected void onPause() {
		// TODO 自動生成されたメソッド・スタブ
		super.onPause();
		finish();
	}
}

</pre>