import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/ResponsiveHelper.dart';
import 'package:angaryos/helper/SessionHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:angaryos/view/LayoutScreen.dart';
import 'package:flutter/material.dart';
import 'package:getwidget/getwidget.dart';
import 'SideMenu.dart';

class MainScreen extends StatelessWidget {
  const MainScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return LayoutScreen(pageWidget: MainPage());
  }
}

class MainPage extends StatefulWidget {
  MainPage({Key? key}) : super(key: key);

  @override
  _MainPageState createState() => _MainPageState();
}

class _MainPageState extends State<MainPage> {
  @override
  void initState() {
    super.initState();
    fillNews();
  }

  List<dynamic> news = [];

  fillNewsFromLocal() async {
    try {
      var temp = await BaseHelper.readFromLocal("news");
      if (temp == null) return false;

      setState(() {
        news = temp;
      });

      return true;
    } catch (e) {
      return false;
    }
  }

  fillNewsFromPipe() {
    try {
      var temp = BaseHelper.readFromPipe("news");
      if (temp == null) return false;

      setState(() {
        news = temp;
      });

      return true;
    } catch (e) {
      return false;
    }
  }

  fillNewsFromServer() async {
    try {
      String url = SessionHelper.getListPageUrl("public_contents", true, true);
      var rt = await SessionHelper.httpGetJsonA(url);
      setState(() {
        news = rt["records"];
      });

      BaseHelper.writeToLocal("news", rt["records"], 1000 * 60 * 60);
      BaseHelper.writeToPipe("news", rt["records"]);
    } catch (e) {}
  }

  fillNews() async {
    if (news.length > 0) return;
    if (fillNewsFromPipe()) return;
    if (await fillNewsFromLocal()) return;
    fillNewsFromServer();
  }

  getNewsCard(item) {
    final List<String> imageList = [
      "https://192.168.10.185/uploads/2020/01/01/b_publicContentImage.jpg",
      "https://192.168.10.185/uploads/2020/01/01/b_publicContentImage.jpg",
      "https://192.168.10.185/uploads/2020/01/01/b_publicContentImage.jpg",
      "https://192.168.10.185/uploads/2020/01/01/b_publicContentImage.jpg",
      "https://192.168.10.185/uploads/2020/01/01/b_publicContentImage.jpg",
    ];

    return Container(
      decoration: BoxDecoration(
          color: secondaryColor,
          borderRadius: const BorderRadius.all(Radius.circular(10))),
      child: Padding(
        padding: const EdgeInsets.all(defaultPadding),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: <Widget>[
            Row(
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    Text(item["name_basic"], style: TextStyle(fontSize: 20)),
                    SizedBox(height: 8),
                    Text(item["created_at"],
                        style: TextStyle(color: Colors.grey)),
                  ],
                ),
                Spacer()
              ],
            ),
            SizedBox(
              height: 10,
            ),
            Text(item["summary"].toString().substring(0, 200) + "..."),
            SizedBox(
              height: 20,
            ),
            Row(
              children: <Widget>[
                ElevatedButton(
                  onPressed: () {},
                  child: Text(tr("Oku")),
                ),
                Spacer(),
                Text(
                  item["user_id"],
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 20,
                  ),
                ),
                SizedBox(width: 16),
              ],
            )
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final List<String> imageList = [
      "https://cdn.pixabay.com/photo/2019/12/20/00/03/road-4707345_960_720.jpg",
      "https://cdn.pixabay.com/photo/2019/12/19/10/55/christmas-market-4705877_960_720.jpg",
      "https://cdn.pixabay.com/photo/2016/11/22/07/09/spruce-1848543__340.jpg",
      "https://cdn.pixabay.com/photo/2019/12/20/00/03/road-4707345_960_720.jpg",
    ];

    return Padding(
        padding:
            const EdgeInsets.fromLTRB(defaultPadding, 0, defaultPadding, 0),
        child: Column(
          children: [
            GFItemsCarousel(
              rowCount: 3,
              children: imageList.map(
                (url) {
                  return Container(
                    margin: EdgeInsets.all(5.0),
                    child: ClipRRect(
                      borderRadius: BorderRadius.all(Radius.circular(5.0)),
                      child: Stack(fit: StackFit.expand, children: [
                        Image.network(url, fit: BoxFit.cover, width: 1000.0),
                        Padding(
                          padding: const EdgeInsets.fromLTRB(8, 0, 0, 10),
                          child: Column(
                            children: [
                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    "Item",
                                    style: TextStyle(
                                      shadows: <Shadow>[
                                        Shadow(
                                          offset: Offset(1.0, 1.0),
                                          blurRadius: 5.0,
                                          color: Color.fromARGB(255, 0, 0, 0),
                                        ),
                                        Shadow(
                                          offset: Offset(1.0, 1.0),
                                          blurRadius: 18.0,
                                          color: Color.fromARGB(125, 0, 0, 255),
                                        ),
                                      ],
                                    ),
                                  ),
                                  IconButton(
                                    padding: EdgeInsets.zero,
                                    icon: Icon(
                                      Icons.more_vert,
                                      color: textWhite,
                                    ),
                                    onPressed: () {},
                                  )
                                ],
                              ),
                            ],
                          ),
                        )
                      ]),
                    ),
                  );
                },
              ).toList(),
            ),
            SizedBox(
              height: 20,
            ),
            GridView.builder(
                physics: NeverScrollableScrollPhysics(),
                shrinkWrap: true,
                itemCount: news.length,
                gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                    childAspectRatio: 3 / 2,
                    crossAxisCount:
                        ResponsiveHelper.getItemCountForOneLine(context),
                    crossAxisSpacing: defaultPadding,
                    mainAxisSpacing: defaultPadding),
                itemBuilder: (context, index) => getNewsCard(news[index])),
          ],
        ));
  }
}
